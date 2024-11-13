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

/********************************************************* */
$filename = "template_" . date('Y-m-d_H:i:s') . ".pptx";
$templateId = '1F1b5c8pKcjqPhH1sgFFkU3YWUg3HIYCTZ3Md0eKpNcE';
$urlTemplate = __DIR__ . "/assets/templates_slides/plantillaBase.pptx";
$urlTemplate = __DIR__ . "/assets/templates_slides/template_2.pptx";
/********************************************************* */


/********************************************************* */
$googleClient = new GoogleClient('client_secret_cuenta_servicio.json');
$driveService = new DriveService($googleClient);
$slidesService = new SlidesService($googleClient);

$presentationManager = new PresentationManager($slidesService);
$fileManager = new FileManager($driveService);
/********************************************************* */


$templateId = $fileManager->uploadFileTemplatePPT($urlTemplate, $filename, $templateId);
$urlTemplateSlide = $fileManager->shareFile($templateId);

$presentationId = $fileManager->copyTemplate($templateId);
$urlPresentation = $fileManager->shareFile($presentationId);

$presentationManager->modifySlides(
    $presentationId,
    [
        "templateId" =>  ["text", "1F1b5c8pKcjqPhH1sgFFkU3YWUg3HIYCTZ3Md0eKpNcE"],
        "codigo_de_cotizacion" =>  ["text", "2024-000123-TDACargo"],
        "empresa_razon_social" =>  ["text", "TDA Cargo"],
        "detalle_servicios" =>  [
            "slide",
            [
                [
                    "nombre" =>  ["text", "Implementación de Sistema CRM"],
                    "detalle_servicios" => [
                        "textList",
                        [
                            "modulo" =>  "Módulo Comercial",
                            "descripcion" =>  "Proceso de Alta de Clientes"
                        ]
                    ]
                ],
                [
                    "nombre" =>  ["text", "Implementación de Sistema CRM 2"],
                    "detalle_servicios" => [
                        "textList",
                        [
                            "modulo" =>  "Módulo Comercial 2",
                            "descripcion" =>  "Proceso de Alta de Clientes 2"
                        ]
                    ]
                ],
                [
                    "nombre" =>  ["text", "Implementación de Sistema CRM 3"],
                    "detalle_servicios" => [
                        "textList",
                        [
                            "modulo" =>  "Módulo Comercial 3",
                            "descripcion" =>  "Proceso de Alta de Clientes 3"
                        ]
                    ]
                ],
            ]
        ],
        "servicios" =>  [
            "slide",
            [
                [
                    "nombre" =>  ["text", "Implementación de Sistema CRM"],
                    "valor" =>  ["text", "1500"],
                    "moneda" =>  ["text", "USD"],
                    "inclusiones" =>  [
                        "list",
                        [
                            "Plataforma 24 x 7 en Google Cloud",
                            "Soporte On Line"
                        ]
                    ],
                    "condiciones_comerciales" =>  [
                        "list",
                        [
                            "Contrato a 12 meses",
                            "Pago al contado",
                            "Emisión de factura el 15 de cada mes"
                        ]
                    ],
                    "duracion_servicio" =>  ["text", "12 meses"],
                    "forma_facturacion" =>  [
                        "list",
                        [
                            "50% de adelanto",
                            "50% al término de la implementación"
                        ]
                    ]
                ],
                [
                    "nombre" =>  ["text", "Implementación de Sistema MULTIPLATAFORMA"],
                    "valor" =>  ["text", "3550"],
                    "moneda" =>  ["text", "USD"],
                    "inclusiones" =>  [
                        "list",
                        [
                            "Plataforma 24 x 7 en Google Cloud",
                            "Soporte On Line"
                        ]
                    ],
                    "condiciones_comerciales" =>  [
                        "list",
                        [
                            "Contrato a 15 meses",
                            "Pago al contado",
                            "Emisión de factura el 20 de cada mes"
                        ]
                    ],
                    "duracion_servicio" =>  ["text", "15 meses"],
                    "forma_facturacion" =>  [
                        "list",
                        [
                            "30% de adelanto",
                            "70% al término de la implementación"
                        ]
                    ]
                ],
            ]
        ]
    ]
);

/********************************************************* */
// $files = $fileManager->getFiles();

// if (empty($files)) {
//     echo "No se encontraron archivos.\n";
// } else {
//     echo "Archivos encontrados:\n";
//     foreach ($files as $key => $file) {
//         echo $key . ". ID: {$file->id}, Nombre: {$file->name}, Tipo MIME: {$file->mimeType}\n";
//     }
// }
/********************************************************* */

echo 'ID TEMPLATE: ' . $templateId . "\n";
echo 'Template url: ' . $urlTemplateSlide . "\n";
echo "URL DEL SLIDES: " . $urlPresentation . "\n";
