<?php


namespace PhantomJs\Procedure;

use Symfony\Component\Config\FileLocatorInterface;
use PhantomJs\Exception\NotExistsException;


class ProcedureLoader implements ProcedureLoaderInterface
{

    public function __construct(protected ProcedureFactoryInterface $procedureFactory, protected FileLocatorInterface $locator)
    {
    }

    public function load(string $id): ProcedureInterface
    {
        $procedure = $this->procedureFactory->createProcedure();
        $procedure->setTemplate($this->loadTemplate($id));

        return $procedure;
    }

    public function loadTemplate(string $id): string
    {
        $path = $this->locator->locate($id);

        return $this->loadFile($path);
    }

    protected function loadFile(string $file): string
    {
        if (!stream_is_local($file)) {
            throw new \InvalidArgumentException(sprintf('Procedure file is not a local file: "%s"', $file));
        }

        if (!file_exists($file)) {
            throw new NotExistsException(sprintf('Procedure file does not exist: "%s"', $file));
        }

        return file_get_contents($file);
    }
}
