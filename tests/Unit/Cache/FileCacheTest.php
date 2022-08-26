<?php


namespace PhantomJs\Tests\Unit\Cache;

use PhantomJs\Cache\FileCache;
use PhantomJs\Tests\TestCase;


class FileCacheTest extends TestCase
{
    protected FileCache $fileCache;

    protected function setUp(): void
    {
        $this->fileCache = new FileCache();
    }

    public function testFalseIsReturnedIfFileDoesNotExist(): void
    {
        $this->assertFalse($this->fileCache->exists('test1'));
    }

    public function testTrueIsReturnedIfFileDoesExist(): void
    {
        $this->fileCache->save('test', 'data');

        $this->assertTrue($this->fileCache->exists('test'));
    }

    public function testFetchData(): void
    {
        $this->fileCache->save('test', 'data');

        $this->assertSame('data', $this->fileCache->fetch('test'));
    }

    public function testGetPath(): void
    {
        $this->fileCache->save('test', 'data');

        $this->assertSame(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test', $this->fileCache->getPath('test'));
    }

    public function testDelete(): void
    {
        $this->fileCache->save('test', 'data');

        $this->assertTrue($this->fileCache->exists('test'));

        $this->fileCache->delete('test');

        $this->assertFalse($this->fileCache->exists('test'));
    }


}
