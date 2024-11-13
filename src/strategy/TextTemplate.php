<?php

namespace GooglePresentation\strategy;

use Exception;

class TextTemplate implements TemplateInterface
{

    public function template(string $key, $data): array
    {

        if (!is_string($data)) throw new Exception('El dato para el tipo "Text" debe ser un string.');

        $requests[] = [
            'replaceAllText' => [
                'containsText' => ['text' => '{{' . $key . '}}', 'matchCase' => true],
                'replaceText' => $data
            ]
        ];

        return $requests;
    }
}
