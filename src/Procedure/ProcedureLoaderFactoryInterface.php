<?php



namespace PhantomJs\Procedure;


interface ProcedureLoaderFactoryInterface
{
    public function createProcedureLoader(string $directory): ProcedureLoaderInterface;
}
