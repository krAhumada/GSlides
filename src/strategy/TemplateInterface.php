<?php

namespace GooglePresentation\strategy;

interface TemplateInterface
{
    public function template(string $key, $data): array;
}
