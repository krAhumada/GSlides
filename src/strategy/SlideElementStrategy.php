<?php

namespace GooglePresentation\strategy;

use GooglePresentation\SlidesService;

use Exception;

class SlideElementStrategy implements ElementInterface
{

    private SlidesService $slidesService;
    private string $presentationId;

    public function __construct(SlidesService $slidesService, string $presentationId)
    {
        $this->slidesService = $slidesService;
        $this->presentationId = $presentationId;
    }

    public function process(string $key, $data, ?string $slideId = null): array
    {

        if (!is_array($data)) throw new Exception('La data para el tipo "Slide" debe ser array');

        $slideObjectId = $this->findSlideObjectId($key);


        list(
            'newSlidesId' => $newSlidesId,
            'requests' => $requests
        ) = $this->duplicateSlide($data, $slideObjectId);

        $cont = 0;

        foreach ($data as $keyNivel1 => $valorNivel1):

            foreach ($valorNivel1 as $keyNivel2 => $valorNivel2):

                $n2TypeElement = (string)$valorNivel2[0];
                $n2DataElement = $valorNivel2[1];

                $element = ElementProcessorFactory::create($n2TypeElement, $this->slidesService, $this->presentationId);


                if (
                    $n2TypeElement == 'text'
                    || $n2TypeElement == 'list'
                ) {

                    $process = $element->process($key . '.' . $keyNivel2, $n2DataElement, $newSlidesId[$cont]);
                } else {

                    $process = $element->process($key, $n2DataElement, $newSlidesId[$cont]);
                }

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

            $cont++;
        endforeach;

        return $requests;
    }
    private function findSlideObjectId(string $keyElement): string
    {

        $slideObjectId = null;

        $slides = $this->slidesService
            ->getSlidesService()
            ->presentations
            ->get($this->presentationId);

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

        return $slideObjectId;
    }

    private function duplicateSlide(array $data, string $slideObjectId): array
    {

        $cont = 0;

        $newSlidesId = [];

        foreach ($data as $element):

            $newSlideObjectId = $slideObjectId;

            if ($cont > 0 && $slideObjectId):

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

            $newSlidesId[] = $newSlideObjectId;

            $cont++;

        endforeach;

        return [
            'newSlidesId' => $newSlidesId,
            'requests' => $requests,
        ];
    }
}
