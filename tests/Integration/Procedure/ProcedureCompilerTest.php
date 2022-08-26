<?php


namespace PhantomJs\Tests\Integration\Procedure;

use PhantomJs\Exception\RequirementException;
use PhantomJs\Exception\SyntaxException;
use PhantomJs\Http\Request;
use PhantomJs\Procedure\ProcedureCompiler;
use PhantomJs\DependencyInjection\ServiceContainer;
use PhantomJs\Procedure\ProcedureInterface;
use PhantomJs\Procedure\ProcedureLoader;
use PhantomJs\Template\TemplateRendererInterface;
use PhantomJs\Tests\TestCase;


class ProcedureCompilerTest extends TestCase
{

    public function testCanCompileProcedure(): void
    {
        $procedureLoader = ServiceContainer::getInstance()->get('procedure_loader')->load('http_default');
        $uncompiled = $procedureLoader->getTemplate();

        $request = new Request('http://example.com');

        $serviceContainer = ServiceContainer::getInstance();

        $compiler = new ProcedureCompiler(
            $serviceContainer->get('phantomjs.procedure.chain_loader'),
            $serviceContainer->get('phantomjs.procedure.procedure_validator'),
            $serviceContainer->get('phantomjs.cache.file_cache'),
            $serviceContainer->get('phantomjs.procedure.template_renderer')
        );

        $compiler->compile($procedureLoader, $request);

        $this->assertNotSame($uncompiled, $procedureLoader->getTemplate());
    }

    public function testProcedureIsLoadedFromCacheIfCacheIsEnabled(): void
    {
        $procedure1 = ServiceContainer::getInstance()->get('procedure_loader')->load('http_default');
        $procedure2 = ServiceContainer::getInstance()->get('procedure_loader')->load('http_default');

        $request = new Request('http://example.com');

        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer->expects($this->exactly(1))
            ->method('render')
            ->will($this->returnValue('var test=1; phantom.exit(1);'));

        $serviceContainer = ServiceContainer::getInstance();

        $compiler = new ProcedureCompiler(
            $serviceContainer->get('phantomjs.procedure.chain_loader'),
            $serviceContainer->get('phantomjs.procedure.procedure_validator'),
            $serviceContainer->get('phantomjs.cache.file_cache'),
            $renderer
        );

        $compiler->enableCache();
        $compiler->compile($procedure1, $request);
        $compiler->compile($procedure2, $request);
    }

    public function testProcedureIsNotLoadedFromCacheIfCacheIsDisabled(): void
    {
        $procedure1 = ServiceContainer::getInstance()->get('procedure_loader')->load('http_default');
        $procedure2 = ServiceContainer::getInstance()->get('procedure_loader')->load('http_default');

        $request = new Request('http://example.com');

        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer->expects($this->exactly(2))
            ->method('render')
            ->will($this->returnValue('var test=1; phantom.exit(1);'));

        $serviceContainer = ServiceContainer::getInstance();

        $compiler = new ProcedureCompiler(
            $serviceContainer->get('phantomjs.procedure.chain_loader'),
            $serviceContainer->get('phantomjs.procedure.procedure_validator'),
            $serviceContainer->get('phantomjs.cache.file_cache'),
            $renderer
        );

        $compiler->disableCache();
        $compiler->compile($procedure1, $request);
        $compiler->compile($procedure2, $request);
    }

    public function testProcedureCacheCanBeCleared(): void
    {
        $procedure1 = ServiceContainer::getInstance()->get('procedure_loader')->load('http_default');
        $procedure2 = ServiceContainer::getInstance()->get('procedure_loader')->load('http_default');

        $request = new Request('http://example.com');

        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer->expects($this->exactly(1))
            ->method('render')
            ->will($this->returnValue('var test=1; phantom.exit(1);'));

        $serviceContainer = ServiceContainer::getInstance();

        $compiler = new ProcedureCompiler(
            $serviceContainer->get('phantomjs.procedure.chain_loader'),
            $serviceContainer->get('phantomjs.procedure.procedure_validator'),
            $serviceContainer->get('phantomjs.cache.file_cache'),
            $renderer
        );

        $compiler->compile($procedure1, $request);
        $compiler->clearCache();
        $compiler->compile($procedure2, $request);
    }

    public function testRequirementExceptionIsThrownIfCompiledTemplateIsNotValid(): void
    {
        $this->expectException(RequirementException::class);

        $template = <<<EOF
    console.log(;
EOF;
        $procedure = ServiceContainer::getInstance()->get('procedure_loader')->load('http_default');
        $procedure->setTemplate($template);

        $request = new Request('http://example.com');

        $serviceContainer = ServiceContainer::getInstance();

        $compiler = new ProcedureCompiler(
            $serviceContainer->get('phantomjs.procedure.chain_loader'),
            $serviceContainer->get('phantomjs.procedure.procedure_validator'),
            $serviceContainer->get('phantomjs.cache.file_cache'),
            $serviceContainer->get('phantomjs.procedure.template_renderer')
        );

        $compiler->compile($procedure, $request);

    }
}
