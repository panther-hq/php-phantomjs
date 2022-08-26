<?php


namespace PhantomJs\Template;


interface TemplateRendererInterface
{
    public function render(string $template, array $context = []): string;
}
