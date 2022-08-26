<?php


namespace PhantomJs\Tests\Unit\Procedure;

use PhantomJs\Procedure\Procedure;
use PhantomJs\Tests\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig_Environment;
use Twig_Loader_String;
use PhantomJs\Engine;
use PhantomJs\Cache\FileCache;
use PhantomJs\Cache\CacheInterface;
use PhantomJs\Parser\JsonParser;
use PhantomJs\Parser\ParserInterface;
use PhantomJs\Template\TemplateRenderer;
use PhantomJs\Template\TemplateRendererInterface;
use PhantomJs\Procedure\ProcedureFactory;


class ProcedureFactoryTest extends TestCase
{
    protected ProcedureFactory $procedureFactory;

    protected function setUp(): void
    {
        $this->procedureFactory = new ProcedureFactory(
            new Engine(),
            new JsonParser(),
            new FileCache(),
            new TemplateRenderer(new Environment(new ArrayLoader()))
        );
    }

    public function testFactoryCanCreateInstanceOfProcedure(): void
    {
        $this->assertInstanceOf(Procedure::class, $this->procedureFactory->createProcedure());
    }

}
