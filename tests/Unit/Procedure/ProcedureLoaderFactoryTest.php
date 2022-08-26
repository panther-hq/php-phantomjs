<?php


namespace PhantomJs\Tests\Unit\Procedure;

use InvalidArgumentException;
use PhantomJs\Procedure\ProcedureFactoryInterface;
use PhantomJs\Procedure\ProcedureLoaderFactory;
use PhantomJs\Procedure\ProcedureLoaderInterface;
use PhantomJs\Tests\TestCase;


class ProcedureLoaderFactoryTest extends TestCase
{
    public function testInvalidArgumentExceptionIsThrownIfDirectoryIsNotReadableWhenCreatingProcedureLoader(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $procedureLoaderFactory = new ProcedureLoaderFactory($this->createMock(ProcedureFactoryInterface::class));
        $procedureLoaderFactory->createProcedureLoader('invalid/directory');
    }

    public function testProcedureLoaderCanBeCreated(): void
    {
        $procedureLoaderFactory = new ProcedureLoaderFactory($this->createMock(ProcedureFactoryInterface::class));
        $procedureLoader = $procedureLoaderFactory->createProcedureLoader(sys_get_temp_dir());

        $this->assertInstanceOf(ProcedureLoaderInterface::class, $procedureLoader);
    }
}
