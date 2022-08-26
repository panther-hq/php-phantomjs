<?php


namespace PhantomJs\Tests\Unit\Procedure;

use Exception;
use PhantomJs\Exception\NotWritableException;
use PhantomJs\Exception\ProcedureFailedException;
use PhantomJs\Tests\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use PhantomJs\Engine;
use PhantomJs\Cache\FileCache;
use PhantomJs\Parser\JsonParser;
use PhantomJs\Template\TemplateRenderer;
use PhantomJs\Procedure\Input;
use PhantomJs\Procedure\Output;
use PhantomJs\Procedure\Procedure;


class ProcedureTest extends TestCase
{
    protected Procedure $procedure;

    protected Engine $engine;

    protected FileCache $fileCache;

    protected function setUp(): void
    {
        $this->engine = $this->createMock(Engine::class);

        $this->procedure = new Procedure(
            $this->engine,
            new JsonParser(),
            new FileCache(),
            new TemplateRenderer(new Environment(new ArrayLoader()))
        );
    }


    public function testProcedureTemplateCanBeSetInProcedure(): void
    {
        $template = 'PROCEDURE_TEMPLATE';

        $this->procedure->setTemplate($template);

        $this->assertSame($this->procedure->getTemplate(), $template);
    }

    public function testProcedureCanBeCompiled(): void
    {
        $template = 'TEST_{{ input.get("uncompiled") }}_PROCEDURE';

        $input  = new Input();
        $input->set('uncompiled', 'COMPILED');

        $this->procedure->setTemplate($template);

        $this->assertSame('TEST_COMPILED_PROCEDURE', $this->procedure->compile($input));
    }


    public function testProcedureFailedExceptionIsThrownIfProcedureCannotBeRun(): void
    {
        $this->expectException(Exception::class);

        $this->procedure->setTemplate('PROCEDURE_TEMPLATE');

        $input  = new Input();
        $output = new Output();

        $this->engine->method('getCommand')
            ->will($this->throwException(new Exception()));


        $this->procedure->run($input, $output);
    }

}
