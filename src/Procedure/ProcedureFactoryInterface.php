<?php



namespace PhantomJs\Procedure;


interface ProcedureFactoryInterface
{
    public function createProcedure(): ProcedureInterface;
}
