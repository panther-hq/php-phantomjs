<?php


namespace PhantomJs\Procedure;


interface ProcedureInterface
{
    public function run(InputInterface $input, OutputInterface $output): void;

    public function setTemplate(string $template): self;

    public function getTemplate(): ?string;

    public function compile(InputInterface $input): string;
}
