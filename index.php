<?php

require 'vendor/autoload.php';

use GooglePresentation\GSlide;

$titlePresentacion = "presentation_" . date('Y-m-d_H:i:s') . "_" . time();

$subirTemplate = true;

$templateId = '1F1b5c8pKcjqPhH1sgFFkU3YWUg3HIYCTZ3Md0eKpNcE';
$urlTemplate = __DIR__ . "/assets/templates_slides/plantillaBase.pptx";
$urlTemplate = __DIR__ . "/assets/templates_slides/template_2.pptx";

$client = new GSlide();

$authentication = $client->auhtentication();

// $client->deleteFilesAccountService($authentication['driveService']);

if ($subirTemplate) {

    $templateId = $client->subirSlides($urlTemplate, $authentication['driveService'], $templateId);

    $urlTemplateSlide = $client->compartirPresentation($templateId, $authentication['driveService']);

    echo 'ID TEMPLATE: ' . $templateId . "\n";
    echo 'Template url: ' . $urlTemplateSlide . "\n";
}

$presentationId = $client->copyTemplate($templateId, $authentication['driveService']);

// $presentationId = $client->createPresentation($titlePresentacion, $authentication['slidesService']);

$urlPresentation = $client->compartirPresentation($presentationId, $authentication['driveService']);

$client->modifySlides(
    $presentationId,
    $authentication['slidesService'],
    [
        '**title**' => 'esta es una titulo de prueba desde php 22',
        '**subtitle**' => 'esta es un sub titulo de prueba desde php 22',
        '**items3**' => 'esta es un sub titulo de prueba desde php',
    ]
);

echo "URL DEL SLIDES: " . $urlPresentation . "\n";

$client->getFilesAccountService($authentication['driveService']);
