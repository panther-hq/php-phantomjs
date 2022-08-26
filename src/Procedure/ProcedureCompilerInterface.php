<?php



namespace PhantomJs\Procedure;


interface ProcedureCompilerInterface
{
    public function compile(ProcedureInterface $procedure, InputInterface $input): void;

    public function load(string $name): string;

    public function enableCache(): void;

    public function disableCache(): void;

    public function clearCache(): void;
}
