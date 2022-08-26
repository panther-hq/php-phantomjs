<?php



namespace PhantomJs\Procedure;


class Output implements OutputInterface
{
    protected array $data = [];

    protected array $logs = [];

    public function import(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function set(string $name,mixed $value): self
    {
        $this->data[$name] = $value;

        return $this;
    }

    public function get(string $name): mixed
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return '';
    }

    public function log(string $data): void
    {
        $this->logs[] = $data;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}
