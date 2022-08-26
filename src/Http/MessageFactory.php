<?php


namespace PhantomJs\Http;


class MessageFactory implements MessageFactoryInterface
{
    private static ?MessageFactory $instance = null;

    public static function getInstance()
    {
        if (!self::$instance instanceof MessageFactoryInterface) {
            self::$instance = new MessageFactory();
        }

        return self::$instance;
    }

    public function createRequest(string $url, string $method = RequestInterface::METHOD_GET, int $timeout = 5000): RequestInterface
    {
        return new Request($url, $method, $timeout);
    }

    public function createCaptureRequest(string $url, string $method = RequestInterface::METHOD_GET, int $timeout = 5000): CaptureRequestInterface
    {
        return new CaptureRequest($url, $method, $timeout);
    }

    public function createPdfRequest(string $url, string $method = RequestInterface::METHOD_GET, int $timeout = 5000): PdfRequestInterface
    {
        return new PdfRequest($url, $method, $timeout);
    }

    public function createResponse(): ResponseInterface
    {
        return new Response();
    }
}
