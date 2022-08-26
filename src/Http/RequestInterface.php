<?php



namespace PhantomJs\Http;


interface RequestInterface
{
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET     = 'GET';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_PATCH   = 'PATCH';

    const REQUEST_TYPE_DEFAULT = 'default';
    const REQUEST_TYPE_CAPTURE = 'capture';
    const REQUEST_TYPE_PDF     = 'pdf';

    public function getType(): string;

    public function setMethod(string $method): self;

    public function getMethod(): string;

    public function setTimeout(int $timeout): self;

    public function getTimeout(): ?int;

    public function setDelay(int $delay): self;

    public function getDelay(): int;

    public function setViewportSize(int $width,int  $height): self;

    public function getViewportWidth(): int;

    public function getViewportHeight(): int;

    public function setUrl(string $url): self;

    public function getUrl(): string;

    public function getBody(): string;

    public function setRequestData(array $data): self;

    public function getRequestData(bool $flat = true): array;

    public function setHeaders(array $headers): self;

    public function addHeader(string $header, string $value): self;

    public function addHeaders(array $headers): self;

    public function getHeaders(string $format = 'default'): array|string;

    public function getSettings();

    public function getCookies(): array;

    public function setBodyStyles(array $styles): self;

    public function getBodyStyles(): array|string;
}
