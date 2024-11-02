<?php

namespace GooglePresentation;

use Google\Service\Drive;
use Exception;

class FileManager
{

    private DriveService $driveService;

    public function __construct(DriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    /*************  ✨ Codeium Command ⭐  *************/
    /**
     * Sube un archivo PPTX a Google Drive y lo convierte a Google Slides.
     *
     * @param string $filePath Ruta local del archivo PPTX a subir.
     * @param string $filename [Opcional] Nombre del archivo en Google Drive.
     *                          Si no se especifica, se utiliza un nombre por defecto.
     * @param string $templateId [Opcional] ID del archivo en Google Drive que se
     *                            va a actualizar. Si se especifica, se actualizará
     *                            el archivo existente en lugar de subir uno nuevo.
     *
     * @return string ID del template recién creado en Google Drive.
     */
    /******  97b17d26-86e5-43bc-a280-fc789d4c4668  *******/
    public function uploadFileTemplatePPT(string $filePath, string $filename = null, string $templateId = null): string
    {

        if (!$filename) $filename = "template_" . date('Y-m-d_H:i:s') . ".pptx";

        if ($templateId) {

            // Buscar el archivo en Google Drive
            $file = $this->driveService->getDriveService()
                ->files
                ->get($templateId, [
                    'fields' => 'id, name, mimeType'
                ]);
        }

        if ($file->getId()) {

            // Actualizar el archivo existente
            $updateFile = new Drive\DriveFile();

            $result = $this->driveService->getDriveService()
                ->files
                ->update(
                    $file->getId(),
                    $updateFile,
                    [
                        'data' => file_get_contents($filePath), // Ruta local del archivo PPTX
                        'mimeType' => 'application/vnd.google-apps.presentation',
                        'uploadType' => 'multipart'
                    ]
                );

            echo "Archivo actualizado con ID: " . $result->getId() . "\n";
        } else {

            // Configuración del archivo para subir
            $fileMetadata = new Drive\DriveFile([
                'name' => $filename,
                'mimeType' => 'application/vnd.google-apps.presentation' // Esto indica que será convertido a Google Slides
            ]);

            // Subir y convertir el archivo
            $file = $this->driveService->getDriveService()
                ->files
                ->create(
                    $fileMetadata,
                    [
                        'data' => file_get_contents($filePath),
                        'mimeType' => 'application/vnd.ms-powerpoint',
                        'uploadType' => 'multipart'
                    ]
                );
        }

        return $file->id;
    }

    /**
     * Copia un template de presentación en Google Drive.
     *
     * @param string $templateId ID del template que se va a copiar.
     * @return string ID del archivo recién creado.
     */
    public function copyTemplate(string $templateId): string
    {

        $fileMetadata = new Drive\DriveFile([
            'name' => "presentation_" . date('Y-m-d_H:i:s') . ".pptx",
        ]);

        $copyFile = $this->driveService->getDriveService()
            ->files
            ->copy($templateId, $fileMetadata);

        return $copyFile->id;
    }

    public function shareFile(string $presentationId): string
    {

        // Compartir la presentación en Google Drive
        $drivePermission = new Drive\Permission([
            'type' => 'anyone',
            'role' => 'writer' // Valid values are 'reader', 'commenter', 'writer', 'fileOrganizer', 'organizer', and 'owner'
        ]);

        $this->driveService->getDriveService()
            ->permissions
            ->create(
                $presentationId,
                $drivePermission,
                ['sendNotificationEmail' => false]
            );

        // Obtener y mostrar el enlace de la presentación en Drive
        $presentationFile = $this->driveService->getDriveService()
            ->files
            ->get(
                $presentationId,
                ['fields' => 'webViewLink']
            );

        $webViewLink = $presentationFile->webViewLink;

        return $webViewLink;
    }

    public function getFiles(): array
    {

        $response = $this->driveService->getDriveService()
            ->files
            ->listFiles([
                // 'pageSize' => 10, // Número de archivos a mostrar
                'fields' => 'nextPageToken, files(id, name, mimeType)', // Campos a obtener
            ]);

        $files = $response->getFiles();

        return $files;
    }

    public function deleteFiles(): void
    {

        try {
            // Listar todos los archivos
            $response = $this->driveService->getDriveService()
                ->files
                ->listFiles([
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
                        $this->driveService->getDriveService()
                            ->files
                            ->delete($file->id);

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
}
