<?php


namespace PhantomJs\Validator;

use Symfony\Component\Config\FileLocatorInterface;


class Esprima implements EngineInterface
{

    protected ?string $esprima = null;

    public function __construct(protected FileLocatorInterface $locator, protected string $file)
    {
    }

    public function toString(): ?string
    {
        $this->load();

        return $this->esprima;
    }

    public function __toString(): string
    {
        return $this->toString() === null ? '' : $this->toString();
    }

    public function load(): ?string
    {
        if (!$this->esprima) {
            $this->esprima = $this->loadFile($this->locator->locate($this->file));
        }

        return $this->esprima;
    }

    protected function loadFile(string $file): ?string
    {
        $content = file_get_contents($file);
        return $content !== false ? $content : null;
    }
}
