<?php


namespace PhantomJs\Tests\Unit\Template;

use PhantomJs\Tests\TestCase;
use PhantomJs\Http\Request;
use PhantomJs\Template\TemplateRenderer;
use Twig\Environment;
use Twig\Loader\ArrayLoader;


class TemplateRendererTest extends TestCase
{
    protected TemplateRenderer $templateRenderer;

    protected function setUp(): void
    {
        $this->templateRenderer = new TemplateRenderer(new Environment(new ArrayLoader()));
    }

    public function testRenderInjectsSingleParameterIntoTemplate(): void
    {
        $template = 'var param = "{{ test }}"';

        $result   = $this->templateRenderer->render($template, ['test' => 'data']);

        $this->assertSame('var param = "data"', $result);
    }

    public function testRenderInjectsMultipleParametersIntoTemplates(): void
    {
        $template = 'var param = "{{ test }}", var param2 = "{{ test2 }}"';

        $result   = $this->templateRenderer->render($template, ['test' => 'data', 'test2' => 'more data']);

        $this->assertSame('var param = "data", var param2 = "more data"', $result);
    }

    public function testRenderInjectsParameterIntoTemplateUsingObjectMethod(): void
    {
        $template = 'var param = {{ request.getTimeout() }}';

        $request = new Request('http://example.com');
        $request->setTimeout(5000);

        $result   = $this->templateRenderer->render($template, ['request' => $request]);

        $this->assertSame('var param = 5000', $result);
    }

    public function testRenderInjectsParameterIntoTemplateUsingObjectMethodWithParameter(): void
    {
        $template = 'var param = {{ request.getHeaders("json") }}';

        $request = new Request('http://example.com');
        $request->addHeader('json', 'test');

        $result = $this->templateRenderer->render($template, ['request' => $request]);

        $this->assertSame(htmlspecialchars('var param = {"json":"test"}'), $result);
    }
}
