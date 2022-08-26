<?php



namespace PhantomJs\Http;

use PhantomJs\Exception\NotWritableException;

class CaptureRequest extends AbstractRequest implements CaptureRequestInterface
{
    protected ?string $type = null;

    protected string $outputFile;

    protected int $rectTop = 0;

    protected int $rectLeft = 0;

    protected int $rectWidth = 0;

    protected int $rectHeight = 0;

    protected string $format = 'jpeg';

    protected int $quality = 75;

    public function getType(): string
    {
        if (!$this->type) {
            return RequestInterface::REQUEST_TYPE_CAPTURE;
        }

        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setCaptureDimensions(int $width,int $height,int $top = 0,int $left = 0): self
    {
        $this->rectWidth = $width;
        $this->rectHeight = $height;
        $this->rectTop = $top;
        $this->rectLeft = $left;

        return $this;
    }

    public function getRectTop(): int
    {
        return $this->rectTop;
    }

    public function getRectLeft(): int
    {
        return $this->rectLeft;
    }

    public function getRectWidth(): int
    {
        return $this->rectWidth;
    }

    public function getRectHeight(): int
    {
        return $this->rectHeight;
    }

    public function setOutputFile(string $file): self
    {
        if (!is_writable(dirname($file))) {
            throw new NotWritableException(sprintf('Output file is not writeable by PhantomJs: %s', $file));
        }

        $this->outputFile = $file;

        return $this;
    }

    public function getOutputFile(): string
    {
        return $this->outputFile;
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

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function setQuality(int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }
}
