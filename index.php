<?php

require 'vendor/autoload.php';

use GooglePresentation\GoogleClient;
use GooglePresentation\DriveService;
use GooglePresentation\SlidesService;
use GooglePresentation\PresentationManager;
use GooglePresentation\FileManager;

/*
Variables de ejemplo
*/

$filename = "template_" . date('Y-m-d_H:i:s') . ".pptx";
$templateId = '1F1b5c8pKcjqPhH1sgFFkU3YWUg3HIYCTZ3Md0eKpNcE';
$urlTemplate = __DIR__ . "/assets/templates_slides/plantillaBase.pptx";
$urlTemplate = __DIR__ . "/assets/templates_slides/template_2.pptx";
/***************************** */

// Configuración inicial
$googleClient = new GoogleClient('client_secret_cuenta_servicio.json');
$driveService = new DriveService($googleClient);
$slidesService = new SlidesService($googleClient);

$presentationManager = new PresentationManager($slidesService);
$fileManager = new FileManager($driveService);

$templateId = $fileManager->uploadFileTemplatePPT($urlTemplate, $filename, $templateId);
$urlTemplateSlide = $fileManager->shareFile($templateId);
$presentationId = $fileManager->copyTemplate($templateId);
$urlPresentation = $fileManager->shareFile($presentationId);

$presentationManager->modifySlides(
    $presentationId,
    [
        '**title**' => 'esta es una titulo de prueba desde php',
        '**subtitle**' => 'esta es un sub titulo de prueba desde php',
        '**items3**' => 'esta es un sub titulo de prueba desde php',
    ]
);


$fileManager->getFiles();

echo 'ID TEMPLATE: ' . $templateId . "\n";
echo 'Template url: ' . $urlTemplateSlide . "\n";
echo "URL DEL SLIDES: " . $urlPresentation . "\n";
