<?php


namespace PhantomJs\Template;


use Twig\Environment;

class TemplateRenderer implements TemplateRendererInterface
{
    public function __construct(protected Environment $twig)
    {
    }

    public function render(string $template, array $context = []): string
    {
        $template = $this->twig->createTemplate($template);

        return $template->render($context);
    }
}
