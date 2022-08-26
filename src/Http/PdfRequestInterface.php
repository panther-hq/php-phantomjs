<?php


namespace PhantomJs\Http;


interface PdfRequestInterface
{
    public function getPaperWidth(): int;

    public function setPaperWidth(int $paperWidth): self;

    public function getPaperHeight(): int;

    public function setPaperHeight(int $paperHeight): self;

    public function setPaperSize(int $width, int $height): self;

    public function getFormat(): string;

    public function setFormat(string $format): self;

    public function getOrientation(): string;

    public function setOrientation(string $orientation): self;

    public function getMargin(): string;

    public function setMargin(string $margin): self;

    public function setRepeatingHeader(string $content, string $height = '1cm'): self;

    public function getRepeatingHeader(): array;

    public function setRepeatingFooter(string $content, string $height = '1cm'): self;

    public function getRepeatingFooter(): array;
}
