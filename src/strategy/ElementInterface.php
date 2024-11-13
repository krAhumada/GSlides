<?php

namespace GooglePresentation\strategy;


// use GooglePresentation\SlidesService;
// use Google\Service\Slides;

interface ElementInterface
{

    // public function __construct(SlidesService $slidesService);

    public function process(string $key, $data, ?string $slideId): array;
}
