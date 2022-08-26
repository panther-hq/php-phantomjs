<?php


namespace PhantomJs\Tests\Unit\Procedure;

use InvalidArgumentException;
use PhantomJs\Procedure\ChainProcedureLoader;
use PhantomJs\Procedure\ProcedureInterface;
use PhantomJs\Procedure\ProcedureLoaderInterface;
use PhantomJs\Tests\TestCase;


class ChainProcedureLoaderTest extends TestCase
{

    public function testInvalidArgumentExceptionIsThrownIfNoValidLoaderCanBeFound(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $procedureLoaders = [];

        $chainProcedureLoader = new ChainProcedureLoader($procedureLoaders);
        $chainProcedureLoader->load('test');
    }

    public function testInstanceOfProcedureIsReturnedIfProcedureIsLoaded(): void
    {
        $procedure = $this->createMock(ProcedureInterface::class);

        $procedureLoader = $this->createMock(ProcedureLoaderInterface::class);
        $procedureLoader->method('load')
            ->will($this->returnValue($procedure));

        $procedureLoaders = [
            $procedureLoader
        ];

        $chainProcedureLoader = new ChainProcedureLoader($procedureLoaders);

        $this->assertInstanceOf(ProcedureInterface::class, $chainProcedureLoader->load('test'));
    }

    public function testLoaderCanBeAddedToChainLoader(): void
    {
        $chainProcedureLoader = new ChainProcedureLoader([]);

        $procedureLoader =  $this->createMock(ProcedureLoaderInterface::class);
        $procedureLoader->expects($this->once())
            ->method('load');

        $chainProcedureLoader->addLoader($procedureLoader);
        $chainProcedureLoader->load('test');
    }

    public function testInvalidArgumentExceptionIsThrownIfNoValidLoaderCanBeFoundWhenLoadingTemplate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $procedureLoaders = [];

        $chainProcedureLoader = new ChainProcedureLoader($procedureLoaders);
        $chainProcedureLoader->loadTemplate('test');
    }

    public function testTemplateIsReturnedIfProcedureTemplateIsLoaded(): void
    {
        $template = 'Test template';

        $procedureLoader = $this->createMock(ProcedureLoaderInterface::class);
        $procedureLoader->method('loadTemplate')
            ->will($this->returnValue($template));

        $procedureLoaders = [
            $procedureLoader
        ];

        $chainProcedureLoader = new ChainProcedureLoader($procedureLoaders);

        $this->assertSame($template, $chainProcedureLoader->loadTemplate('test'));
    }

}
