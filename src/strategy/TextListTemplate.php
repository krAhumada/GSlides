<?php

namespace GooglePresentation\strategy;

use Exception;

class TextListTemplate implements TextTemplate
{

    public function template(string $key, $data): array
    {

        if (!is_array($data)) throw new Exception('El dato para el tipo "TextList" debe ser un array.');

        foreach ($data as $key2 => $arrayText):

            $requests[] = [
                'replaceAllText' => [
                    'containsText' => ['text' => '{{' . $key . '.' . $key2 . '}}', 'matchCase' => true],
                    'replaceText' => $arrayText
                ]
            ];


        endforeach;

        return $requests;
    }
}
