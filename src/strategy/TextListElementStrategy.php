<?php

namespace GooglePresentation\strategy;

use Exception;

class TextListElementStrategy implements ElementInterface
{

    public function process(string $key, $data, ?string $slideId): array
    {

        if (!is_array($data)) throw new Exception('El dato para el tipo "TextList" debe ser un array.');

        foreach ($data as $key2 => $arrayText):

            $requests[] = [
                'replaceAllText' => [
                    'containsText' => [
                        'text' => '{{' . $key . '.' . $key2 . '}}',
                        'matchCase' => true
                    ],
                    'replaceText' => $arrayText,
                    'pageObjectIds' => $slideId ? ["$slideId"] : [],
                ]
            ];


        endforeach;

        return $requests;
    }
}
