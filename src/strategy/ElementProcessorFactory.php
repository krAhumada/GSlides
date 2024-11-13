<?php

namespace GooglePresentation\strategy;

use GooglePresentation\strategy\{TextElementStrategy, ListElementStrategy, TextListElementStrategy, SlideElementStrategy};

use GooglePresentation\SlidesService;

class ElementProcessorFactory
{
    public static function create(string $typeElement, ?SlidesService $slidesService, string $presentationId): ElementInterface
    {

        switch ($typeElement) {
            case 'text':
                $element = new TextElementStrategy();
                break;
            case 'list':
                $element = new ListElementStrategy();
                break;
            case 'textList':
                $element = new TextListElementStrategy();
                break;
            case 'slide':
                $element = new SlideElementStrategy($slidesService, $presentationId);
                break;
            default:
                throw new \Exception('Elemento no reconocido: ' . $typeElement);
        }

        return $element;
    }
}
