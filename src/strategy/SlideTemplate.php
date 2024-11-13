<?php

namespace GooglePresentation\strategy;

use Exception;

class SlideTemplate implements TemplateInterface
{
    public function template(string $key, $data): array
    {

        if (!is_array($data)) throw new Exception('La data para el tipo "Slide" debe ser array');

        $valueInString = '';

        foreach ($data as $keyNivel1 => $valorNivel1):
            foreach ($valorNivel1 as $keyNivel2 => $valorNivel2):

                $n2TypeElement = (string)$valorNivel2[0];
                $n2DataElement = $valorNivel2[1];

                $process =  $this->proccessTypeElement($n2TypeElement, $n2DataElement, $key);

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
        endforeach;

        $requests[] = [
            'replaceAllText' => [
                'containsText' => ['text' => '{{' . $key . '}}', 'matchCase' => true],
                'replaceText' => $valueInString
            ]
        ];

        return $requests;
    }
}
