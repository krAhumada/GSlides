<?php

namespace GooglePresentation\strategy;

use Exception;

class ListElementStrategy implements ElementInterface
{

    public function process(string $key, $data, ?string $slideId): array
    {

        if (!is_array($data)) throw new Exception('El dato para el tipo "List" debe ser un array.');

        $valueInString = '';

        foreach ($data as $keyNivel1 => $valorNivel1):

            if (is_array($valorNivel1)) {

                foreach ($valorNivel1 as $keyNivel2 => $valorNivel2):

                    if (is_array($valorNivel2)) {

                        foreach ($valorNivel2 as $keyNivel3 => $valorNivel3):

                            $valueInString .= $valorNivel3 . "\n";
                        endforeach;
                    } else {

                        $valueInString .= $valorNivel2 . "\n";
                    }

                endforeach;
            } else {

                $valueInString .= $valorNivel1 . "\n";
            }

        endforeach;

        $valueInString = trim($valueInString);

        $valueInString = str_replace("\n\n", "\n", $valueInString);

        $requests[] = [
            'replaceAllText' => [
                'containsText' => [
                    'text' => '{{' . $key . '}}',
                    'matchCase' => true
                ],
                'replaceText' => $valueInString,
                'pageObjectIds' => $slideId ? ["$slideId"] : [],
            ]
        ];

        return $requests;
    }
}
