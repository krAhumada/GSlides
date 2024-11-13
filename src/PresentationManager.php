<?php

namespace GooglePresentation;

use Google\Service\Slides;

class PresentationManager
{

    private SlidesService $slidesService;

    private string $presentationId;
    public function __construct(SlidesService $slidesService)
    {
        $this->slidesService = $slidesService;
    }

    public function modifySlides(string $presentationId, array $valores): void
    {
        $requests = [];

        $this->presentationId = $presentationId;

        foreach ($valores as $key1 => $valor):

            $typeElement = (string)$valor[0];
            $dataElement = $valor[1];

            $process = $this->proccessTypeElement($typeElement, $dataElement, $key1);

            if (!empty($process)) {

                if (is_array($process)) {

                    foreach ($process as $item) {

                        $requests[] = $item;
                    }
                } else {

                    $requests[] = $process;
                }
            }

        endforeach;

        // echo '================================================================================';
        // echo json_encode($requests);

        $this->slidesService->getSlidesService()
            ->presentations
            ->batchUpdate(
                $presentationId,
                new Slides\BatchUpdatePresentationRequest(['requests' => $requests])
            );
    }

    private function proccessTypeElement(string $typeElement, $dataElement, string $keyElement, string $slideId = null): array
    {

        $requests = [];

        switch ($typeElement):
            case 'text':

                if (is_string($dataElement)):

                    if ($slideId) {

                        $requests[] = [
                            'replaceAllText' => [
                                'containsText' => ['text' => '{{' . $keyElement . '}}', 'matchCase' => true],
                                'replaceText' => $dataElement,
                                'pageObjectIds' => ["$slideId"],
                            ],
                        ];
                    } else {

                        $requests[] = [
                            'replaceAllText' => [
                                'containsText' => ['text' => '{{' . $keyElement . '}}', 'matchCase' => true],
                                'replaceText' => $dataElement,
                            ]
                        ];
                    }

                endif;

                break;
            case 'textList':

                if (is_array($dataElement)):

                    foreach ($dataElement as $key2 => $arrayText):

                        if ($slideId) {

                            $requests[] = [
                                'replaceAllText' => [
                                    'containsText' => ['text' => '{{' . $keyElement . '.' . $key2 . '}}', 'matchCase' => true],
                                    'replaceText' => $arrayText,
                                    'pageObjectIds' => ["$slideId"],
                                ],
                            ];
                        } else {

                            $requests[] = [
                                'replaceAllText' => [
                                    'containsText' => ['text' => '{{' . $keyElement . '.' . $key2 . '}}', 'matchCase' => true],
                                    'replaceText' => $arrayText,
                                ]
                            ];
                        }

                    endforeach;

                endif;

                break;
            case 'list':

                if (is_array($dataElement)):

                    $valueInString = '';

                    foreach ($dataElement as $keyNivel1 => $valorNivel1):

                        if (is_array($valorNivel1)) {

                            foreach ($valorNivel1 as $keyNivel2 => $valorNivel2):

                                if (is_array($valorNivel2)) {

                                    foreach ($valorNivel2 as $keyNivel3 => $valorNivel3):

                                        $valueInString .= $valorNivel3 . "\n";
                                    endforeach;
                                } else {

                                    $valueInString .= $valorNivel2 . "\n";
                                }

                            endforeach;
                        } else {

                            $valueInString .= $valorNivel1 . "\n";
                        }

                    endforeach;

                    $valueInString = trim($valueInString);

                    $valueInString = str_replace("\n\n", "\n", $valueInString);

                    if ($slideId) {

                        $requests[] = [
                            'replaceAllText' => [
                                'containsText' => ['text' => '{{' . $keyElement . '}}', 'matchCase' => true],
                                'replaceText' => $valueInString,
                                'pageObjectIds' => ["$slideId"],
                            ],
                        ];
                    } else {

                        $requests[] = [
                            'replaceAllText' => [
                                'containsText' => ['text' => '{{' . $keyElement . '}}', 'matchCase' => true],
                                'replaceText' => $valueInString,
                            ]
                        ];
                    }

                endif;

                break;
            case 'slide':

                if (is_array($dataElement)):

                    $valueInString = '';

                    /*****NEW SLIDE */

                    $slides = $this->slidesService
                        ->getSlidesService()
                        ->presentations
                        ->get($this->presentationId);

                    // $countSlides = count($slides);

                    $slideObjectId = null;

                    foreach ($slides as $slide):

                        $slideId = $slide->getObjectId();

                        foreach ($slide->getPageElements() as $element):

                            if ($element->getShape() && $element->getShape()->getText()):

                                $textContent = $element->getShape()->getText()->getTextElements();

                                foreach ($textContent as $textElement):

                                    if (
                                        $textElement->getTextRun()
                                        && strpos($textElement->getTextRun()->getContent(), "#$keyElement") !== false
                                    ) {
                                        $slideObjectId = $slideId;
                                        break 3;
                                    }
                                endforeach;
                            endif;
                        endforeach;
                    endforeach;

                    $cont = 0;

                    $newSlidesId = [];

                    foreach ($dataElement as $keyNivel1 => $valorNivel1):

                        $newSlideObjectId = $slideObjectId;

                        if ($cont > 0 && $slideObjectId):

                            // $countSlides++;

                            $newSlideObjectId = "SLIDES_API" . time() . "_" . ($cont - 1);

                            $requests[] = [
                                'duplicateObject' => [
                                    'objectId' => $slideObjectId,
                                    'objectIds' => [
                                        "$slideObjectId" => "$newSlideObjectId"
                                    ]
                                ]
                            ];

                        endif;

                        print_r("slidesID =====> " . $newSlideObjectId . "\n");

                        $newSlidesId[] = $newSlideObjectId;

                        $cont++;
                    endforeach;

                    $cont = 0;

                    foreach ($dataElement as $keyNivel1 => $valorNivel1):

                        foreach ($valorNivel1 as $keyNivel2 => $valorNivel2):

                            $n2TypeElement = (string)$valorNivel2[0];
                            $n2DataElement = $valorNivel2[1];

                            if ($n2TypeElement == 'text') {

                                $process =  $this->proccessTypeElement($n2TypeElement, $n2DataElement, $keyElement . '.' . $keyNivel2, $newSlidesId[$cont]);
                            } else if ($n2TypeElement == 'list') {

                                $process =  $this->proccessTypeElement($n2TypeElement, $n2DataElement, $keyElement . '.' . $keyNivel2, $newSlidesId[$cont]);
                            } else {

                                $process =  $this->proccessTypeElement($n2TypeElement, $n2DataElement, $keyElement, $newSlidesId[$cont]);
                            }


                            if (!empty($process)) {

                                // print_r($process);

                                if (is_array($process)) {

                                    foreach ($process as $item) {

                                        $requests[] = $item;
                                    }
                                } else {

                                    $requests[] = $process;
                                }
                            }
                        endforeach;

                        $cont++;
                    endforeach;

                // echo json_encode($requests);

                endif;

                break;

            default:
                # code...
                break;
        endswitch;

        return $requests;
    }
}
