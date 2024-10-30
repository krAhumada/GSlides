<?php

namespace GooglePresentation;

use Google\Service\Slides;

class SlidesService
{

    private Slides $slidesService;
    public function __construct(GoogleClient $googleClient)
    {
        $this->slidesService = new Slides($googleClient->getClient());
    }

    public function getSlidesService(): Slides
    {
        return $this->slidesService;
    }
}
