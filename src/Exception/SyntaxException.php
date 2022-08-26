<?php


namespace PhantomJs\Exception;


class SyntaxException extends PhantomJsException
{
    public function __construct(string $exception,protected array $errors = [])
    {
        parent::__construct($exception);
    }

     public function getErrors(): array
    {
        return $this->errors;
    }
}
