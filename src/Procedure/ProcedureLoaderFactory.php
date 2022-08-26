<?php


namespace PhantomJs\Procedure;

use Symfony\Component\Config\FileLocator;


class ProcedureLoaderFactory implements ProcedureLoaderFactoryInterface
{
    public function __construct(protected ProcedureFactoryInterface $procedureFactory)
    {
    }

    public function createProcedureLoader(string $directory): ProcedureLoader
    {
        return new ProcedureLoader(
            $this->procedureFactory,
            $this->createFileLocator($directory)
        );
    }

    protected function createFileLocator(string $directory): FileLocator
    {
        if (!is_dir($directory) || !is_readable($directory)) {
            throw new \InvalidArgumentException(sprintf('Could not create procedure loader as directory does not exist or is not readable: "%s"', $directory));
        }

        return new FileLocator($directory);
    }
}
