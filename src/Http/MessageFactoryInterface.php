<?php


namespace PhantomJs\Http;


interface MessageFactoryInterface
{
    public static function getInstance();

    public function createRequest(string $url, string $method = RequestInterface::METHOD_GET, int $timeout = 5000): RequestInterface;

    public function createCaptureRequest(string $url, string $method = RequestInterface::METHOD_GET, int $timeout = 5000): CaptureRequestInterface;

    public function createPdfRequest(string $url, string $method = RequestInterface::METHOD_GET, int $timeout = 5000): PdfRequestInterface;

    public function createResponse(): ResponseInterface;
}
