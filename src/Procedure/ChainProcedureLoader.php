<?php


namespace PhantomJs\Procedure;


class ChainProcedureLoader implements ProcedureLoaderInterface
{

    public function __construct(protected array $procedureLoaders)
    {
    }

    public function addLoader(ProcedureLoaderInterface $procedureLoader): void
    {
        array_unshift($this->procedureLoaders, $procedureLoader);
    }


    public function load(string $id): ProcedureInterface
    {
        /** @var \PhantomJs\Procedure\ProcedureLoaderInterface $loader **/
        foreach ($this->procedureLoaders as $loader) {

            try {

                $procedure = $loader->load($id);

                return $procedure;

           } catch (\Exception $e) {}

        }

        throw new \InvalidArgumentException(sprintf('No valid procedure loader could be found to load the \'%s\' procedure.', $id));
    }

    public function loadTemplate(string $id): string
    {
        /** @var \PhantomJs\Procedure\ProcedureLoaderInterface $loader **/
        foreach ($this->procedureLoaders as $loader) {

            try {

                $template = $loader->loadTemplate($id);

                return $template;

            } catch (\Exception $e) {}

        }

        throw new \InvalidArgumentException(sprintf('No valid procedure loader could be found to load the \'%s\' procedure template.', $id));
    }
}
