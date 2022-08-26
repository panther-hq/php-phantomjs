<?php

namespace PhantomJs;

use PhantomJs\Procedure\ProcedureLoaderInterface;
use PhantomJs\Http\MessageFactoryInterface;
use PhantomJs\Http\RequestInterface;
use PhantomJs\Http\ResponseInterface;

interface ClientInterface
{
    public static function getInstance(): self;

    public function getEngine(): Engine;

    public function getMessageFactory(): MessageFactoryInterface;

    public function getProcedureLoader(): ProcedureLoaderInterface;

    public function send(RequestInterface $request, ResponseInterface $response): ResponseInterface;

    public function getLog(): string;

    public function setProcedure(string $procedure): void;

    public function getProcedure(): string;

    public function isLazy(): void;
}
