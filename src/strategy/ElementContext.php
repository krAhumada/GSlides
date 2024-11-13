<?php

namespace GooglePresentation\strategy;

class ElementContext
{

    private ElementInterface $tipoTemplate;

    public function __construct(ElementInterface $tipoTemplate) {}

    public function setTemplate(ElementInterface $tipoTemplate)
    {


        $this->tipoTemplate = $tipoTemplate;
    }
}
