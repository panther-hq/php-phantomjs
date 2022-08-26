<?php



namespace PhantomJs\Http;

/**
 * PHP PhantomJs.
 *
 * @author Jon Wenmoth <contact@jonnyw.me>
 */
interface CaptureRequestInterface
{
    public function setCaptureDimensions(int $width,int $height,int $top = 0,int $left = 0): self;

    public function getRectTop(): int;

    public function getRectLeft(): int;

    public function getRectWidth(): int;

    public function getRectHeight(): int;

    public function setOutputFile(string $file): self;

    public function getOutputFile(): string;

    public function getFormat(): string;

    public function setFormat(string $format): self;

    public function getQuality(): int;

    public function setQuality(int $quality): self;
}
