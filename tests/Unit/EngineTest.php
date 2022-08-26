<?php

namespace PhantomJs\Tests\Unit;

use PhantomJs\Engine;
use PhantomJs\Exception\InvalidExecutableException;
use PhantomJs\Tests\TestCase;

class EngineTest extends TestCase
{
    protected Engine $engine;

    protected function setUp(): void
    {
        $this->engine = new Engine();
    }

    public function testInvalidExecutableExceptionIsThrownIfPhantomJSPathIsInvalid(): void
    {
        $this->expectException(InvalidExecutableException::class);

        $this->engine->setPath('/invalid/phantomjs/path');
    }

    public function testDefaultPhantomJSPathIsReturnedIfNoCustomPathIsSet(): void
    {
        $this->assertSame('bin/phantomjs', $this->engine->getPath());
    }

    public function testCanLogData(): void
    {
        $log = 'Test log info';

        $this->engine->log($log);

        $this->assertSame($log, $this->engine->getLog());
    }

    public function testCanClearLog(): void
    {
        $log = 'Test log info';

        $this->engine->log($log);
        $this->engine->clearLog();

        $this->assertEmpty($this->engine->getLog());
    }

    public function testCanAddRunOption(): void
    {
        $options = [
            'option1',
            'option2'
        ];

        $this->engine->setOptions($options);
        $this->engine->addOption('option3');

        array_push($options, 'option3');

        $this->assertSame($options, $this->engine->getOptions());
    }

    public function testInvalidExecutableExceptionIsThrownWhenBuildingCommandIfPathToPhantomJSIsInvalid(): void
    {
        $this->expectException(InvalidExecutableException::class);


        $phantomJs = new \ReflectionProperty(get_class($this->engine), 'path');
        $phantomJs->setAccessible(true);
        $phantomJs->setValue($this->engine, 'invalid/path');

        $this->engine->getCommand();
    }

    public function testCommandContainsPhantomJSExecutable(): void
    {
        $this->assertSame($this->engine->getPath(), $this->engine->getCommand());
    }

    public function testDebugFlagCanBeSet(): void
    {
        $this->engine->debug(true);

        $this->assertSame("{$this->engine->getPath()} --debug=true", $this->engine->getCommand());
    }

    public function testDebugFlagIsNotSetIfDebuggingIsNotEnabled(): void
    {
        $this->engine->debug(false);

        $this->assertSame($this->engine->getPath(), $this->engine->getCommand());
    }

    public function testDiskCacheFlagCanBeSet(): void
    {
        $this->engine->cache(true);

        $this->assertSame("{$this->engine->getPath()} --disk-cache=true", $this->engine->getCommand());
    }

    public function testDiskCacheFlagIsNotSetIfCachingIsNotEnabled(): void
    {
        $this->engine->cache(false);

        $this->assertSame($this->engine->getPath(), $this->engine->getCommand());
    }

    public function testCommandContainsRunOptions(): void
    {
        $option1 = '--local-storage-path=/some/path';
        $option2 = '--local-storage-quota=5';
        $option3 = '--local-to-remote-url-access=true';

        $this->engine->addOption($option1);
        $this->engine->addOption($option2);
        $this->engine->addOption($option3);

        $this->assertSame("{$this->engine->getPath()} {$option1} {$option2} {$option3}", $this->engine->getCommand());
    }

    public function testDebugFlagIsSetIfRunOptionsAreAlsoSet(): void
    {
        $option = '--local-storage-path=/some/path';

        $this->engine->addOption($option);
        $this->engine->debug(true);

        $this->assertSame("{$this->engine->getPath()} {$option} --debug=true", $this->engine->getCommand());
    }
}
