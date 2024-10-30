<?php

namespace GooglePresentation;

use Google\Service\Slides;

class PresentationManager
{

    private SlidesService $slidesService;
    public function __construct(SlidesService $slidesService)
    {
        $this->slidesService = $slidesService;
    }

    public function modifySlides(string $presentationId, array $valores): void
    {
        $requests = [];

        foreach ($valores as $key => $valor) {
            $requests[] = [
                'replaceAllText' => [
                    'containsText' => ['text' => $key, 'matchCase' => true],
                    'replaceText' => $valor
                ]
            ];
        }

        $this->slidesService->getSlidesService()
            ->presentations
            ->batchUpdate(
                $presentationId,
                new Slides\BatchUpdatePresentationRequest(['requests' => $requests])
            );
    }
}
