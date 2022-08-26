<?php


namespace JonnyW\PhantomJs\Tests\Unit\Http;

use PhantomJs\Http\Response;
use PhantomJs\Tests\TestCase;


class ResponseTest extends TestCase
{
    protected Response $response;

    protected function setUp(): void
    {
        $this->response = new Response();
    }

    public function testStatusCanBeImported(): void
    {
        $data = [
            'status' => 200
        ];

        $this->response->import($data);

        $this->assertEquals(200, $this->response->getStatus());
    }

    public function testContentCanBeImported(): void
    {
        $data = [
            'content' => 'Test content'
        ];

        $this->response->import($data);

        $this->assertEquals('Test content', $this->response->getContent());
    }

    public function testContentTypeCanBeImported(): void
    {
        $data = [
            'contentType' => 'text/html'
        ];

        $this->response->import($data);

        $this->assertEquals('text/html', $this->response->getContentType());
    }

    public function testUrlCanBeImported(): void
    {
        $data = [
            'url' => 'http://test.com'
        ];

        $this->response->import($data);

        $this->assertEquals('http://test.com', $this->response->getUrl());
    }

    public function testRedirectUrlCanBeImported(): void
    {
        $data = [
            'redirectURL' => 'http://test.com'
        ];

        $this->response->import($data);

        $this->assertEquals('http://test.com', $this->response->getRedirectUrl());
    }

    public function testTimeCanBeImported(): void
    {
        $data = [
            'time' => 123456789
        ];

        $this->response->import($data);

        $this->assertEquals(123456789, $this->response->getTime());
    }

    public function testHeadersCanBeImported(): void
    {
        $headers = [
            [
                'name'  => 'Header1',
                'value' => 'Test Header 1'
            ]
        ];

        $data = [
            'headers' => $headers
        ];

        $this->response->import($data);

        $expectedHeaders = [
            $headers[0]['name'] => $headers[0]['value']
        ];

        $this->assertEquals($expectedHeaders, $this->response->getHeaders());
    }

    public function testNullIsReturnedIfHeaderIsNotSet():void
    {
        $this->assertNull($this->response->getHeader('invalid_header'));
    }

    public function testCanGetHeader(): void
    {
        $headers = [
            [
                'name'  => 'Header1',
                'value' => 'Test Header 1'
            ]
        ];

        $data = [
            'headers' => $headers
        ];

        $this->response->import($data);

        $this->assertEquals('Test Header 1', $this->response->getHeader('Header1'));
    }

    public function testIsRedirectIfStatusCodeIs300(): void
    {
        $data = [
            'status' => 300
        ];

        $this->response->import($data);

        $this->assertTrue($this->response->isRedirect());
    }

    public function testIsRedirectIfStatusCodeIs301(): void
    {
        $data = [
            'status' => 301
        ];

        $this->response->import($data);

        $this->assertTrue($this->response->isRedirect());
    }

    public function testIsRedirectIfStatusCodeIs302(): void
    {
        $data = [
            'status' => 302
        ];

        $this->response->import($data);

        $this->assertTrue($this->response->isRedirect());
    }

    public function testIsRedirectIfStatusCodeIs303(): void
    {
        $data = [
            'status' => 303
        ];

        $this->response->import($data);

        $this->assertTrue($this->response->isRedirect());
    }

    public function testIsRedirectIfStatusCodeIs304(): void
    {
        $data = [
            'status' => 304
        ];

        $this->response->import($data);

        $this->assertTrue($this->response->isRedirect());
    }

    public function testIsRedirectIfStatusCodeIs305(): void
    {
        $data = [
            'status' => 305
        ];

        $this->response->import($data);

        $this->assertTrue($this->response->isRedirect());
    }

    public function testIsRedirectIfStatusCodeIs306(): void
    {
        $data = [
            'status' => 306
        ];

        $this->response->import($data);

        $this->assertTrue($this->response->isRedirect());
    }

    public function testIsRedirectIfStatusCodeIs307(): void
    {
        $data = [
            'status' => 307
        ];

        $this->response->import($data);

        $this->assertTrue($this->response->isRedirect());
    }

    public function testIsNotRedirectIfStatusCodeIsNotRedirect(): void
    {
        $data = [
            'status' => 401
        ];

        $this->response->import($data);

        $this->assertFalse($this->response->isRedirect());
    }

    public function testCookiesCanBeImported(): void
    {
        $cookie = 'cookie=TESTING; HttpOnly; expires=Mon, 16-Nov-2020 00:00:00 GMT; domain=.jonnyw.kiwi; path=/';
        $data = [
            'cookies' => [$cookie]
        ];

        $this->response->import($data);

        $this->assertContains($cookie, $this->response->getCookies());
    }

}
