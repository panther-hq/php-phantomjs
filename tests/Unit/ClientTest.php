<?php


namespace PhantomJs\Tests\Unit;

use PhantomJs\Client;
use PhantomJs\Engine;
use PhantomJs\Http\MessageFactoryInterface;
use PhantomJs\Procedure\ProcedureLoaderInterface;
use PhantomJs\Procedure\ProcedureCompilerInterface;
use PhantomJs\Tests\TestCase;


class ClientTest extends TestCase
{
    protected Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(
            $this->createMock(Engine::class),
            $this->createMock(ProcedureLoaderInterface::class),
            $this->createMock(ProcedureCompilerInterface::class),
            $this->createMock(MessageFactoryInterface::class)
        );
    }


    public function testCanGetClientThroughFactoryMethod(): void
    {
        $this->assertInstanceOf(Client::class, Client::getInstance());
    }

    public function testCanGetEngine(): void
    {
        $this->assertInstanceOf(Engine::class, $this->client->getEngine());
    }

    public function testCanGetMessageFactory(): void
    {
        $this->assertInstanceOf(MessageFactoryInterface::class, $this->client->getMessageFactory());
    }

    public function testCanGetProcedureLoader(): void
    {
        $this->assertInstanceOf(ProcedureLoaderInterface::class, $this->client->getProcedureLoader());
    }

    public function testCanGetProcedureCompiler(): void
    {
        $this->assertInstanceOf(ProcedureCompilerInterface::class, $this->client->getProcedureCompiler());
    }




}
