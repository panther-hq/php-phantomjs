<?php


namespace PhantomJs\Tests\Unit\Http;

use PhantomJs\Exception\InvalidMethodException;
use PhantomJs\Exception\NotWritableException;
use PhantomJs\Http\CaptureRequest;
use PhantomJs\Http\RequestInterface;
use PhantomJs\Tests\TestCase;


class CaptureRequestTest extends TestCase
{
    protected CaptureRequest $captureRequest;
    
    protected function setUp(): void
    {
        $this->captureRequest = new CaptureRequest('http://example.com', RequestInterface::METHOD_GET, 5000);
    }

    public function testCaptureTypeIsReturnedByDefaultIfNotTypeIsSet(): void
    {
        $this->assertEquals(RequestInterface::REQUEST_TYPE_CAPTURE, $this->captureRequest->getType());
    }
    
    public function testCustomTypeCanBeSet(): void
    {
        $requestType = 'testType';

        $this->captureRequest->setType($requestType);

        $this->assertEquals($requestType, $this->captureRequest->getType());
    }
    
    public function testUrlCanBeSetViaConstructor(): void
    {
        $this->assertEquals('http://example.com', $this->captureRequest->getUrl());
    }

    public function testMethodCanBeSetViaConstructor(): void
    {
        $this->assertEquals(RequestInterface::METHOD_GET, $this->captureRequest->getMethod());
    }

    public function testTimeoutCanBeSetViaConstructor(): void
    {
        $this->assertEquals(5000, $this->captureRequest->getTimeout());
    }

    public function testInvalidMethodIsThrownIfMethodIsInvalid(): void
    {
        $this->expectException(InvalidMethodException::class);

        $this->captureRequest->setMethod('INVALID_METHOD');
    }

    public function testRectWidthCanBeSet(): void
    {
        $width  = 100;
        $height = 200;

        $this->captureRequest->setCaptureDimensions($width, $height);

        $this->assertEquals($width, $this->captureRequest->getRectWidth());
    }

    public function testRectHeightCanBeSet(): void
    {
        $width  = 100;
        $height = 200;

        $this->captureRequest->setCaptureDimensions($width, $height);

        $this->assertEquals($height, $this->captureRequest->getRectHeight());
    }

    public function testRectTopCanBeSet(): void
    {
        $width  = 100;
        $height = 200;
        $top    = 50;

        $this->captureRequest->setCaptureDimensions($width, $height, $top);

        $this->assertEquals($top, $this->captureRequest->getRectTop());
    }

    public function testRectLeftCanBeSet(): void
    {
        $width  = 100;
        $height = 200;
        $left   = 50;

        $this->captureRequest->setCaptureDimensions($width, $height, 0, $left);

        $this->assertEquals($left, $this->captureRequest->getRectLeft());
    }

    public function testUrlDoesNotContainQueryParamsIfMethodIsNotHeadOrGet(): void
    {
        $url = 'http://example.com';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        
        $this->captureRequest->setMethod('POST');
        $this->captureRequest->setUrl($url);
        $this->captureRequest->setRequestData($data);

        $this->assertEquals($url, $this->captureRequest->getUrl());
    }

    public function testUrlDoesContainQueryParamsIfMethodIsGet(): void
    {
        $url = 'http://example.com';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        
        $this->captureRequest->setMethod('GET');
        $this->captureRequest->setUrl($url);
        $this->captureRequest->setRequestData($data);

        $expectedUrl = $url . '?test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($expectedUrl, $this->captureRequest->getUrl());
    }

    public function testUrlDoesContainQueryParamsIfMethodIsHead(): void
    {
        $url = 'http://example.com';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        
        $this->captureRequest->setMethod('HEAD');
        $this->captureRequest->setUrl($url);
        $this->captureRequest->setRequestData($data);

        $expectedUrl = $url . '?test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($expectedUrl, $this->captureRequest->getUrl());
    }

    public function testQueryParamsAreAppendedToUrlIfUrlContainsExistingQueryParams(): void
    {
        $url = 'http://example.com?existing_param=Existing';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->captureRequest->setMethod('GET');
        $this->captureRequest->setUrl($url);
        $this->captureRequest->setRequestData($data);

        $expectedUrl = $url . '&test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($expectedUrl, $this->captureRequest->getUrl());
    }

    public function testRequestContainsNoBodyIfMethodIsGet(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->captureRequest->setMethod('GET');
        $this->captureRequest->setRequestData($data);

        $this->assertEquals('', $this->captureRequest->getBody());
    }

    public function testRequestContainsNoBodyIfMethodIsHead(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->captureRequest->setMethod('HEAD');
        $this->captureRequest->setRequestData($data);

        $this->assertEquals('', $this->captureRequest->getBody());
    }

    public function testRequestContainsABodyIfMethodIsNotHeadOrGet(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->captureRequest->setMethod('POST');
        $this->captureRequest->setRequestData($data);

        $body = 'test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($body, $this->captureRequest->getBody());
    }

    public function testRequestDataCanBeFalttened(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => [
                'Testing2',
                'Testing3'
            ]
        ];

        $this->captureRequest->setRequestData($data);

        $flatData = [
            'test_param1'    => 'Testing1',
            'test_param2[0]' => 'Testing2',
            'test_param2[1]' => 'Testing3'
        ];

        $this->assertEquals($flatData, $this->captureRequest->getRequestData(true));
    }

    public function testRawRequestDataCanBeAccessed(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => [
                'Testing2',
                'Testing3'
            ]
        ];

        $this->captureRequest->setRequestData($data);

        $this->assertEquals($data, $this->captureRequest->getRequestData(false));
    }

    public function testHeadersCanBeAdded(): void
    {
        $existingHeaders = [
            'Header1' => 'Header 1'
        ];

        $newHeaders = [
            'Header2' => 'Header 2',
            'Header3' => 'Header 3'
        ];

        $this->captureRequest->setHeaders($existingHeaders);
        $this->captureRequest->addHeaders($newHeaders);

        $expectedHeaders = array_merge($existingHeaders, $newHeaders);

        $this->assertEquals($expectedHeaders, $this->captureRequest->getHeaders());
    }

    public function testHeadersCanBeAccessedInJsonFormat(): void
    {
        $headers = [
            'Header1' => 'Header 1',
            'Header2' => 'Header 2'
        ];

        $this->captureRequest->setHeaders($headers);

        $expectedHeaders = json_encode($headers);

        $this->assertEquals($expectedHeaders, $this->captureRequest->getHeaders('json'));
    }

    public function testRawHeadersCanBeAccessed(): void
    {
        $headers = [
            'Header1' => 'Header 1',
            'Header2' => 'Header 2'
        ];

        $this->captureRequest->setHeaders($headers);

        $this->assertEquals($headers, $this->captureRequest->getHeaders('default'));
    }

    public function testNotWritableExceptonIsThrownIfOutputPathIsNotWritable(): void
    {
        $this->expectException(NotWritableException::class);

        $invalidPath = '/invalid/path';

        $this->captureRequest->setOutputFile($invalidPath);
    }

    public function testCanSetOutputFile(): void
    {
        $outputFile = sprintf('%s/test.jpg', sys_get_temp_dir());

        $this->captureRequest->setOutputFile($outputFile);

        $this->assertEquals($outputFile, $this->captureRequest->getOutputFile());
    }

    public function testCanSetViewportWidth(): void
    {
        $width  = 100;
        $height = 200;

        $this->captureRequest->setViewportSize($width, $height);

        $this->assertEquals($width, $this->captureRequest->getViewportWidth());
    }

    public function testCanSetViewportHeight(): void
    {
        $width  = 100;
        $height = 200;

        $this->captureRequest->setViewportSize($width, $height);

        $this->assertEquals($height, $this->captureRequest->getViewportHeight());
    }

}
