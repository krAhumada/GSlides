<?php

namespace GooglePresentation;

use Google\Service\Slides;
use GooglePresentation\strategy\ElementProcessorFactory;

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

        foreach ($valores as $key1 => $valor):

            $typeElement = (string)$valor[0];
            $dataElement = $valor[1];

            $processor = ElementProcessorFactory::create($typeElement, $this->slidesService, $presentationId);

            $process = $processor->process($key1, $dataElement, null);

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

        $this->slidesService->getSlidesService()
            ->presentations
            ->batchUpdate(
                $presentationId,
                new Slides\BatchUpdatePresentationRequest(['requests' => $requests])
            );
    }
}
