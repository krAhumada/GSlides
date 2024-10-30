<?php

namespace GooglePresentation;

// require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;
use Google\Service\Slides;
use Google\Service\Slides\Request;

use Exception;

class GSlide
{

    public function __construct() {}

    public function auhtentication()
    {

        $client = new Client();
        $client->setAuthConfig('client_secret_cuenta_servicio.json');
        $client->addScope([Slides::PRESENTATIONS, Drive::DRIVE]);

        $slidesService = new Slides($client);
        $driveService = new Drive($client);

        return [
            // 'client' => $client,
            'slidesService' => $slidesService,
            'driveService' => $driveService
        ];
    }

    public function createPresentation(string $title, Slides $slidesService): string
    {

        // Crear una nueva presentación
        $presentation = new Slides\Presentation([
            'title' => $title
        ]);

        $presentation = $slidesService->presentations->create($presentation);

        $presentationId = $presentation->presentationId;

        return $presentationId;
    }

    public function compartirPresentation(string $presentationId, object $driveService): string
    {

        // Compartir la presentación en Google Drive
        $drivePermission = new Drive\Permission([
            'type' => 'anyone',
            'role' => 'writer' // Valid values are 'reader', 'commenter', 'writer', 'fileOrganizer', 'organizer', and 'owner'
        ]);

        $driveService->permissions->create(
            $presentationId,
            $drivePermission,
            ['sendNotificationEmail' => false]
        );

        // Obtener y mostrar el enlace de la presentación en Drive
        $presentationFile = $driveService->files->get($presentationId, ['fields' => 'webViewLink']);
        $webViewLink = $presentationFile->webViewLink;

        return $webViewLink;
    }

    public function subirSlides(string $filePath, Drive $driveService, string $templateId = null): string
    {

        // $files = [];

        $filename = "template_" . date('Y-m-d_H:i:s') . ".pptx";

        if ($templateId) {

            // Buscar el archivo en Google Drive
            $file = $driveService->files->get($templateId, [
                'fields' => 'id, name, mimeType'
            ]);
        }

        if ($file->getId()) {

            // Actualizar el archivo existente
            $updateFile = new Drive\DriveFile();

            $result = $driveService->files->update($file->getId(), $updateFile, [
                'data' => file_get_contents($filePath), // Ruta local del archivo PPTX
                'mimeType' => 'application/vnd.google-apps.presentation',
                'uploadType' => 'multipart'
            ]);

            echo "Archivo actualizado con ID: " . $result->getId() . "\n";
        } else {

            // Configuración del archivo para subir
            $fileMetadata = new Drive\DriveFile([
                'name' => $filename,
                'mimeType' => 'application/vnd.google-apps.presentation' // Esto indica que será convertido a Google Slides
            ]);

            // Subir y convertir el archivo
            $file = $driveService->files->create($fileMetadata, [
                'data' => file_get_contents($filePath),
                'mimeType' => 'application/vnd.ms-powerpoint',
                'uploadType' => 'multipart'
            ]);
        }

        return  $file->id;
    }

    public function copyTemplate(string $templateId, Drive $driveService): string
    {

        $fileMetadata = new Drive\DriveFile([
            'name' => date('Y-m-d_H:i:s') . ".pptx",
        ]);

        $copyFile = $driveService->files->copy($templateId, $fileMetadata);

        return $copyFile->id;
    }

    public function getFilesAccountService(Drive $driveService): void
    {
        // Listar archivos en Google Drive
        try {
            $response = $driveService->files->listFiles([
                // 'pageSize' => 10, // Número de archivos a mostrar
                'fields' => 'nextPageToken, files(id, name, mimeType)', // Campos a obtener
            ]);

            $files = $response->getFiles();

            if (empty($files)) {
                echo "No se encontraron archivos.\n";
            } else {
                echo "Archivos encontrados:\n";
                foreach ($files as $key => $file) {
                    echo $key . ". ID: {$file->id}, Nombre: {$file->name}, Tipo MIME: {$file->mimeType}\n";
                }
            }
        } catch (Exception $e) {
            die('Error al listar archivos: ' . $e->getMessage());
        }
    }

    public function deleteFilesAccountService(Drive $driveService): void
    {

        try {
            // Listar todos los archivos
            $response = $driveService->files->listFiles([
                'fields' => 'files(id, name)', // Obtener solo ID y nombre de cada archivo
                'pageSize' => 1000              // Ajusta el tamaño de la página según necesites
            ]);

            $files = $response->getFiles();

            if (empty($files)) {
                echo "No se encontraron archivos.\n";
            } else {
                echo "Eliminando archivos...\n";
                foreach ($files as $file) {
                    try {
                        $driveService->files->delete($file->id);
                        echo "Archivo eliminado: {$file->name} (ID: {$file->id})\n";
                    } catch (Exception $e) {
                        echo "Error al eliminar el archivo {$file->name} (ID: {$file->id}): " . $e->getMessage() . "\n";
                    }
                }
            }
        } catch (Exception $e) {
            echo 'Error al listar o eliminar archivos: ' . $e->getMessage();
        }
    }




    public function modifySlides(string $presentationId, Slides $slidesService, array $valores)
    {

        $slides = $slidesService->presentations->get($presentationId)->getSlides();

        $slideId = $slides[0]->getObjectId();

        $requests = [];

        foreach ($valores as $key => $valor) {
            $requests[] = [
                'replaceAllText' => [
                    'containsText' => [
                        'text' => $key,
                        'matchCase' => true
                    ],
                    'replaceText' => $valor
                ]
            ];
        }

        // Ejecuta las solicitudes de modificación
        $slidesService->presentations->batchUpdate($presentationId, new Slides\BatchUpdatePresentationRequest([
            'requests' => $requests
        ]));
    }
}
