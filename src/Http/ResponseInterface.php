<?php



namespace PhantomJs\Http;


interface ResponseInterface
{
    public function import(array $data): self;

    public function getHeaders(): array;

    public function getHeader(string $code): ?string;

    public function getStatus(): ?int;

    public function getContent(): ?string;

    public function getContentType(): string;

    public function getUrl(): string;

    public function getRedirectUrl(): string;

    public function isRedirect(): bool;

    public function getTime(): string;

    public function getCookies(): array;
}
