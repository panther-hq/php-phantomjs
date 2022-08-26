<?php


namespace PhantomJs\Http;


class PdfRequest extends CaptureRequest implements PdfRequestInterface
{
    protected int $paperWidth = 0;

    protected int $paperHeight = 0;

    protected string $format = 'A4';

    protected string $orientation = 'portrait';

    protected string $margin = '1cm';

    protected array $header = [];

    protected array $footer = [];


    public function getType(): string
    {
        if (!$this->type) {
            return RequestInterface::REQUEST_TYPE_PDF;
        }

        return $this->type;
    }

    public function getPaperWidth(): int
    {
        return $this->paperWidth;
    }

    public function setPaperWidth(int $paperWidth): self
    {
        $this->paperWidth = $paperWidth;

        return $this;
    }

    public function getPaperHeight(): int
    {
        return $this->paperHeight;
    }

    public function setPaperHeight(int $paperHeight): self
    {
        $this->paperHeight = $paperHeight;

        return $this;
    }

    public function setPaperSize(int $width, int $height): self
    {
        $this->paperWidth = $width;
        $this->paperHeight = $height;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getOrientation(): string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getMargin(): string
    {
        return $this->margin;
    }

    public function setMargin(string $margin): self
    {
        $this->margin = $margin;

        return $this;
    }


    public function setRepeatingHeader(string $content, string $height = '1cm'): self
    {
        $this->header = [
            'content' => $content,
            'height' => $height
        ];

        return $this;
    }

    public function getRepeatingHeader(): array
    {
        return $this->header;
    }

    public function setRepeatingFooter(string $content, string $height = '1cm'): self
    {
        $this->footer = [
            'content' => $content,
            'height' => $height
        ];

        return $this;
    }

    public function getRepeatingFooter(): array
    {
        return $this->footer;
    }
}
