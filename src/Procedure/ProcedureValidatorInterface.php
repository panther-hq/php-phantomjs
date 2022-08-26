<?php


namespace PhantomJs\Procedure;


interface ProcedureValidatorInterface
{
    public function validate(string $procedure): bool;
}
