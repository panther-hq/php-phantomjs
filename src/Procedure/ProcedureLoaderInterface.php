<?php


namespace PhantomJs\Procedure;


interface ProcedureLoaderInterface
{
    public function load(string $id): ProcedureInterface;

    public function loadTemplate(string $id): string;
}
