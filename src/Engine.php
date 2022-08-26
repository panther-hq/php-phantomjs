<?php

namespace PhantomJs;

use PhantomJs\Exception\InvalidExecutableException;

class Engine
{
    protected string $path;

    protected bool $debug = false;

    protected bool $cache = false;

    protected array $options = [];

    protected string $log;

    public function __construct()
    {
        $this->path = 'bin/phantomjs';
    }

    public function getCommand(): string
    {
        $path = $this->getPath();
        $options = $this->getOptions();

        $this->validateExecutable($path);

        if ($this->cache) {
            array_push($options, '--disk-cache=true');
        }

        if ($this->debug) {
            array_push($options, '--debug=true');
        }

        return trim(sprintf('%s %s', $path, implode(' ', $options)));
    }

    public function setPath(string $path): self
    {
        $this->validateExecutable($path);

        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function addOption(string $option): self
    {
        if (!in_array($option, $this->options)) {
            $this->options[] = $option;
        }

        return $this;
    }

    public function debug(bool $doDebug): self
    {
        $this->debug = $doDebug;

        return $this;
    }

    public function cache(bool $doCache): self
    {
        $this->cache = $doCache;

        return $this;
    }

    public function log(string $info): self
    {
        $this->log = $info;

        return $this;
    }

    public function getLog(): string
    {
        return $this->log;
    }

    public function clearLog(): self
    {
        $this->log = '';

        return $this;
    }

    private function validateExecutable(string $file): bool
    {
        if (!file_exists($file) || !is_executable($file)) {
            throw new InvalidExecutableException(sprintf('File does not exist or is not executable: %s', $file));
        }

        return true;
    }
}
