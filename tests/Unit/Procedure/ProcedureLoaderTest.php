<?php


namespace PhantomJs\Tests\Unit\Procedure;

use InvalidArgumentException;
use PhantomJs\Exception\NotExistsException;
use PhantomJs\Procedure\ProcedureInterface;
use PhantomJs\Tests\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig_Environment;
use Twig_Loader_String;
use Symfony\Component\Config\FileLocatorInterface;
use PhantomJs\Engine;
use PhantomJs\Cache\FileCache;
use PhantomJs\Cache\CacheInterface;
use PhantomJs\Parser\JsonParser;
use PhantomJs\Parser\ParserInterface;
use PhantomJs\Template\TemplateRenderer;
use PhantomJs\Template\TemplateRendererInterface;
use PhantomJs\Procedure\ProcedureFactory;
use PhantomJs\Procedure\ProcedureFactoryInterface;
use PhantomJs\Procedure\ProcedureLoader;


class ProcedureLoaderTest extends TestCase
{
    protected ProcedureFactory $procedureFactory;

    protected FileCache $fileCache;

    public function setUp(): void
    {
        $this->fileCache = new FileCache();
        $this->procedureFactory = new ProcedureFactory(
            new Engine(),
            new JsonParser(),
            $this->fileCache,
            new TemplateRenderer(new Environment(new ArrayLoader()))
        );
    }

    public function testInvalidArgumentExceptionIsThrownIfProcedureFileIsNotLocal(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $fileLocator = $this->createMock(FileLocatorInterface::class);

        $fileLocator->method('locate')
            ->will($this->returnValue('http://example.com/index.html'));

        $procedureLoader = new ProcedureLoader($this->procedureFactory, $fileLocator);
        $procedureLoader->load('test');
    }

    public function testNotExistsExceptionIsThrownIfProcedureFileDoesNotExist(): void
    {
        $this->expectException(NotExistsException::class);

        $fileLocator = $this->createMock(FileLocatorInterface::class);

        $fileLocator->method('locate')
            ->will($this->returnValue('/invalid/file.proc'));

        $procedureLoader = new ProcedureLoader($this->procedureFactory, $fileLocator);
        $procedureLoader->load('test');
    }

    public function testProcedureCanBeLoaded(): void
    {
        $body = 'TEST_PROCEDURE';
        $this->fileCache->save('test', $body);

        $fileLocator = $this->createMock(FileLocatorInterface::class);

        $fileLocator->method('locate')
            ->will($this->returnValue($this->fileCache->getPath('test')));

        $procedureLoader = new ProcedureLoader($this->procedureFactory, $fileLocator);

        $this->assertInstanceOf(ProcedureInterface::class, $procedureLoader->load('test'));
    }

    public function testProcedureTemplateIsSetInProcedureInstance(): void
    {
        $body = 'TEST_PROCEDURE';
        $this->fileCache->save('test', $body);

        $fileLocator = $this->createMock(FileLocatorInterface::class);

        $fileLocator->method('locate')
            ->will($this->returnValue($this->fileCache->getPath('test')));

        $procedureLoader = new ProcedureLoader($this->procedureFactory, $fileLocator);

        $this->assertSame($body, $procedureLoader->load('test')->getTemplate());
    }

    public function testProcedureTemplateCanBeLoaded(): void
    {
        $body = 'TEST_PROCEDURE';
        $this->fileCache->save('test', $body);

        $fileLocator = $this->createMock(FileLocatorInterface::class);

        $fileLocator->method('locate')
            ->will($this->returnValue($this->fileCache->getPath('test')));

        $procedureLoader = new ProcedureLoader($this->procedureFactory, $fileLocator);

        $this->assertNotNull($procedureLoader->loadTemplate('test'));
    }
}
