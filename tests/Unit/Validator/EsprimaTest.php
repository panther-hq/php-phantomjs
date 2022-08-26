<?php


namespace PhantomJs\Tests\Unit\Validator;

use InvalidArgumentException;
use PhantomJs\Tests\TestCase;
use Symfony\Component\Config\FileLocator;
use PhantomJs\Validator\Esprima;


class EsprimaTest extends TestCase
{
    protected FileLocator $fileLocator;

    protected function setUp(): void
    {
        $this->fileLocator = new FileLocator(realpath(sprintf('%s/../../../src/Resources/validators/', __DIR__)));
    }


    public function testInvalidArgumentExceptionIsThrownIfFilePathIsNotLocalFile(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $esprima = new Esprima($this->fileLocator, 'http://example.com');

        $esprima->load();
    }

    public function testInvalidArgumentIsThrownIfFileDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $esprima = new Esprima($this->fileLocator, 'invalidFile.js');

        $esprima->load();
    }

    public function testEngineCanBeLoaded(): void
    {
        $esprima = new Esprima($this->fileLocator, 'esprima-2.0.0.js');

        $this->assertStringContainsString('esprima', $esprima->load());
    }

    public function testEngineCanBeCovertedToString(): void
    {
        $esprima = new Esprima($this->fileLocator, 'esprima-2.0.0.js');

        $this->assertStringContainsString('esprima', $esprima);
    }


}
