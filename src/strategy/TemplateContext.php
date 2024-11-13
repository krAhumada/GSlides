<?php

namespace GooglePresentation\strategy;

class TemplateContext
{

    private TemplateInterface $tipoTemplate;

    public function __construct(TemplateInterface $tipoTemplate) {}

    public function setTemplate(TemplateInterface $tipoTemplate)
    {


        $this->tipoTemplate = $tipoTemplate;
    }
}
