<?php

namespace GooglePresentation\strategy;

use Exception;

class TextElementStrategy implements ElementInterface
{

    public function process(string $key, $data, ?string $slideId): array
    {

        if (!is_string($data)) throw new Exception('El dato para el tipo "Text" debe ser un string.');

        $requests[] = [
            'replaceAllText' => [
                'containsText' => [
                    'text' => '{{' . $key . '}}',
                    'matchCase' => true
                ],
                'replaceText' => $data,
                'pageObjectIds' => $slideId ? ["$slideId"] : [],
            ]
        ];

        return $requests;
    }
}
