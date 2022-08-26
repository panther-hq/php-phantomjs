<?php


namespace PhantomJs\Tests\Unit\Http;

use PhantomJs\Exception\InvalidMethodException;
use PhantomJs\Exception\NotWritableException;
use PhantomJs\Http\PdfRequest;
use PhantomJs\Http\Request;
use PhantomJs\Http\RequestInterface;
use PhantomJs\Tests\TestCase;


class PdfRequestTest extends TestCase
{
    protected PdfRequest $pdfRequest;

    protected function setUp(): void
    {
        $this->pdfRequest = new PdfRequest('http://example.com', RequestInterface::METHOD_GET, 5000);
    }

    public function testPdfTypeIsReturnedByDefaultIfNotTypeIsSet(): void
    {
        $this->assertEquals(RequestInterface::REQUEST_TYPE_PDF, $this->pdfRequest->getType());
    }

    public function testCustomTypeCanBeSet(): void
    {
        $requestType = 'testType';

        $this->pdfRequest->setType($requestType);

        $this->assertEquals($requestType, $this->pdfRequest->getType());
    }

    public function testUrlCanBeSetViaConstructor(): void
    {
        $url = 'http://example.com';

        $this->assertEquals($url, $this->pdfRequest->getUrl());
    }

    public function testMethodCanBeSetViaConstructor(): void
    {
        $this->assertEquals(RequestInterface::METHOD_GET, $this->pdfRequest->getMethod());
    }

    public function testTimeoutCanBeSetViaConstructor(): void
    {
        $this->assertEquals(5000, $this->pdfRequest->getTimeout());
    }

    public function testInvalidMethodIsThrownIfMethodIsInvalid(): void
    {
        $this->expectException(InvalidMethodException::class);

        $this->pdfRequest->setMethod('INVALID_METHOD');
    }

    public function testUrlDoesNotContainQueryParamsIfMethodIsNotHeadOrGet(): void
    {
        $url = 'http://example.com';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->pdfRequest->setMethod('POST');
        $this->pdfRequest->setUrl($url);
        $this->pdfRequest->setRequestData($data);

        $this->assertEquals($url, $this->pdfRequest->getUrl());
    }

    public function testUrlDoesContainQueryParamsIfMethodIsGet(): void
    {
        $url = 'http://example.com';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->pdfRequest->setMethod('GET');
        $this->pdfRequest->setUrl($url);
        $this->pdfRequest->setRequestData($data);

        $expectedUrl = $url . '?test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($expectedUrl, $this->pdfRequest->getUrl());
    }

    public function testUrlDoesContainQueryParamsIfMethodIsHead(): void
    {
        $url = 'http://example.com';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->pdfRequest->setMethod('HEAD');
        $this->pdfRequest->setUrl($url);
        $this->pdfRequest->setRequestData($data);

        $expectedUrl = $url . '?test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($expectedUrl, $this->pdfRequest->getUrl());
    }

    public function testQueryParamsAreAppendedToUrlIfUrlContainsExistingQueryParams(): void
    {
        $url = 'http://example.com?existing_param=Existing';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->pdfRequest->setMethod('GET');
        $this->pdfRequest->setUrl($url);
        $this->pdfRequest->setRequestData($data);

        $expectedUrl = $url . '&test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($expectedUrl, $this->pdfRequest->getUrl());
    }

    public function testRequestContainsNoBodyIfMethodIsGet(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->pdfRequest->setMethod('GET');
        $this->pdfRequest->setRequestData($data);

        $this->assertEquals('', $this->pdfRequest->getBody());
    }

    public function testRequestContainsNoBodyIfMethodIsHead(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->pdfRequest->setMethod('HEAD');
        $this->pdfRequest->setRequestData($data);

        $this->assertEquals('', $this->pdfRequest->getBody());
    }

    public function testRequestContainsABodyIfMethodIsNotHeadOrGet(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->pdfRequest->setMethod('POST');
        $this->pdfRequest->setRequestData($data);

        $body = 'test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($body, $this->pdfRequest->getBody());
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

        $this->pdfRequest->setRequestData($data);

        $flatData = [
            'test_param1'    => 'Testing1',
            'test_param2[0]' => 'Testing2',
            'test_param2[1]' => 'Testing3'
        ];

        $this->assertEquals($flatData, $this->pdfRequest->getRequestData(true));
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

        $this->pdfRequest->setRequestData($data);

        $this->assertEquals($data, $this->pdfRequest->getRequestData(false));
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

        $this->pdfRequest->setHeaders($existingHeaders);
        $this->pdfRequest->addHeaders($newHeaders);

        $expectedHeaders = array_merge($existingHeaders, $newHeaders);

        $this->assertEquals($expectedHeaders, $this->pdfRequest->getHeaders());
    }

    public function testHeadersCanBeAccessedInJsonFormat(): void
    {
        $headers = [
            'Header1' => 'Header 1',
            'Header2' => 'Header 2'
        ];

        $this->pdfRequest->setHeaders($headers);

        $expectedHeaders = json_encode($headers);

        $this->assertEquals($expectedHeaders, $this->pdfRequest->getHeaders('json'));
    }

    public function testRawHeadersCanBeAccessed(): void
    {
        $headers = [
            'Header1' => 'Header 1',
            'Header2' => 'Header 2'
        ];

        $this->pdfRequest->setHeaders($headers);

        $this->assertEquals($headers, $this->pdfRequest->getHeaders('default'));
    }

    public function tesNotWritableExceptonIsThrownIfOutputPathIsNotWritable(): void
    {
        $this->expectException(NotWritableException::class);

        $invalidPath = '/invalid/path';

        $this->pdfRequest->setOutputFile($invalidPath);
    }

    public function testCanSetOutputFile(): void
    {
        $outputFile = sprintf('%s/test.jpg', sys_get_temp_dir());

        $this->pdfRequest->setOutputFile($outputFile);

        $this->assertEquals($outputFile, $this->pdfRequest->getOutputFile());
    }

    public function testCanSetViewportWidth(): void
    {
        $width  = 100;
        $height = 200;

        $this->pdfRequest->setViewportSize($width, $height);

        $this->assertEquals($width, $this->pdfRequest->getViewportWidth());
    }

    public function testCanSetViewportHeight(): void
    {
        $width  = 100;
        $height = 200;

        $this->pdfRequest->setViewportSize($width, $height);

        $this->assertEquals($height, $this->pdfRequest->getViewportHeight());
    }

    public function testCanSetPaperWidth(): void
    {
        $width  = 10;
        $height = 20;

        $this->pdfRequest->setPaperSize($width, $height);

        $this->assertEquals($width, $this->pdfRequest->getPaperWidth());
    }

    public function testCanSetPaperHeight(): void
    {
        $width  = 10;
        $height = 20;

        $this->pdfRequest->setPaperSize($width, $height);

        $this->assertEquals($height, $this->pdfRequest->getPaperHeight());
    }

}
