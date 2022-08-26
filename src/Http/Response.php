<?php


namespace PhantomJs\Http;

use PhantomJs\Procedure\OutputInterface;

class Response implements ResponseInterface, OutputInterface
{
    public array $headers;

    public ?int $status = null;

    public ?string $content = null;

    public ?string $contentType = null;

    public string $url;

    public ?string $redirectURL = null;

    public string $time;

    public array $console;

    public array $cookies;

    public function import(array $data): self
    {
        foreach ($data as $param => $value) {

            if ($param === 'headers') {
                continue;
            }

            if (property_exists($this, $param)) {
                $this->$param = $value;
            }
        }

        $this->headers = array();

        if (isset($data['headers'])) {
            $this->setHeaders((array) $data['headers']);
        }

        return $this;
    }

    protected function setHeaders(array $headers): self
    {
        foreach ($headers as $header) {

            if (isset($header['name']) && isset($header['value'])) {
                $this->headers[$header['name']] = $header['value'];
            }
        }

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $code): ?string
    {
        if (isset($this->headers[$code])) {
            return $this->headers[$code];
        }

        return null;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectURL;
    }

    public function isRedirect(): bool
    {
        $status = $this->getStatus();

        return $status >= 300 && $status <= 307;
    }

    public function getTime(): string
    {
        return $this->time;
    }

    public function getConsole(): array
    {
        return $this->console;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }
}
