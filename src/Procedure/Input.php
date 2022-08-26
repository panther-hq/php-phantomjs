<?php



namespace PhantomJs\Procedure;


class Input implements InputInterface
{
    protected array $data = [];

    public function set(string $name, ?string $value): self
    {
        $this->data[$name] = $value;

        return $this;
    }

    public function get(string $name): ?string
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }
}
