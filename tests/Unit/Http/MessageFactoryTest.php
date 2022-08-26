<?php


namespace PhantomJs\Tests\Unit\Http;

use PhantomJs\Http\CaptureRequest;
use PhantomJs\Http\MessageFactory;
use PhantomJs\Http\Request;
use PhantomJs\Http\Response;
use PhantomJs\Tests\TestCase;


class MessageFactoryTest extends TestCase
{
    protected MessageFactory $messageFactory;

    protected function setUp(): void
    {
        $this->messageFactory = new MessageFactory();
    }

    public function testFactoryMethodCreatesMessageFactory(): void
    {
        $this->assertInstanceOf(MessageFactory::class, MessageFactory::getInstance());
    }

    public function testCanCreateRequest(): void
    {
        $this->assertInstanceOf(Request::class, $this->messageFactory->createRequest('http://example.com'));
    }

    public function testCanCreateRequestWithUrl(): void
    {
        $url = 'http://example.com';

        $request = $this->messageFactory->createRequest($url);

        $this->assertEquals($url, $request->getUrl());
    }

    public function testCanCreateRequestWithMethod(): void
    {
        $method = 'POST';

        $request = $this->messageFactory->createRequest('http://example.com', $method);

        $this->assertEquals($method, $request->getMethod());
    }

    public function testCanCreateRequestWithTimeout(): void
    {
        $timeout = 123456789;

        $request = $this->messageFactory->createRequest('http://example.com', 'GET', $timeout);

        $this->assertEquals($timeout, $request->getTimeout());
    }

    public function testCanCreateCaptureRequest(): void
    {
        $this->assertInstanceOf(CaptureRequest::class, $this->messageFactory->createCaptureRequest('http://example.com'));
    }

    public function testCanCreateCaptureRequestWithUrl(): void
    {
        $url = 'http://example.com';

        $captureRequest = $this->messageFactory->createCaptureRequest($url);

        $this->assertEquals($url, $captureRequest->getUrl());
    }

    public function testCanCreateCaptureRequestWithMethod(): void
    {
        $method = 'POST';

        $captureRequest = $this->messageFactory->createCaptureRequest('http://example.com', $method);

        $this->assertEquals($method, $captureRequest->getMethod());
    }

    public function testCanCreateCaptureRequestWithTimeout(): void
    {
        $timeout = 123456789;

        $captureRequest = $this->messageFactory->createCaptureRequest('http://example.com', 'GET', $timeout);

        $this->assertEquals($timeout, $captureRequest->getTimeout());
    }

    public function testCanCreateResponse(): void
    {
        $this->assertInstanceOf(Response::class, $this->messageFactory->createResponse());
    }

}
