<?php


namespace JonnyW\PhantomJs\Tests\Unit\Http;

use PhantomJs\Exception\InvalidMethodException;
use PhantomJs\Http\Request;
use PhantomJs\Http\RequestInterface;
use PhantomJs\Tests\TestCase;


class RequestTest extends TestCase
{
    protected Request $request;

    protected function setUp(): void
    {
        $this->request = new Request('http://example.com', RequestInterface::METHOD_GET, 5000);
    }


    public function testDefaultTypeIsReturnedByDefaultIfNotTypeIsSet(): void
    {
        $this->assertEquals(RequestInterface::REQUEST_TYPE_DEFAULT, $this->request->getType());
    }

    public function testCustomTypeCanBeSet(): void
    {
        $this->requestType = 'testType';

        $this->request->setType($this->requestType);

        $this->assertEquals($this->requestType, $this->request->getType());
    }

    public function testUrlCanBeSetViaConstructor(): void
    {
        $this->assertEquals('http://example.com', $this->request->getUrl());
    }

    public function testMethodCanBeSetViaConstructor(): void
    {
        $this->assertEquals(RequestInterface::METHOD_GET, $this->request->getMethod());
    }

    public function testTimeoutCanBeSetViaConstructor(): void
    {
        $this->assertEquals(5000, $this->request->getTimeout());
    }

    public function testInvalidMethodIsThrownIfMethodIsInvalid(): void
    {
        $this->expectException(InvalidMethodException::class);

        $this->request->setMethod('INVALID_METHOD');
    }

    public function testUrlDoesNotContainQueryParamsIfMethodIsNotHeadOrGet(): void
    {
        $url = 'http://example.com';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->request->setMethod('POST');
        $this->request->setUrl($url);
        $this->request->setRequestData($data);

        $this->assertEquals($url, $this->request->getUrl());
    }

    public function testUrlDoesContainQueryParamsIfMethodIsGet(): void
    {
        $url = 'http://example.com';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];
        
        $this->request->setMethod('GET');
        $this->request->setUrl($url);
        $this->request->setRequestData($data);

        $expectedUrl = $url . '?test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($expectedUrl, $this->request->getUrl());
    }

    public function testUrlDoesContainQueryParamsIfMethodIsHead(): void
    {
        $url = 'http://example.com';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->request->setMethod('HEAD');
        $this->request->setUrl($url);
        $this->request->setRequestData($data);

        $expectedUrl = $url . '?test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($expectedUrl, $this->request->getUrl());
    }

    public function testQueryParamsAreAppendedToUrlIfUrlContainsExistingQueryParams(): void
    {
        $url = 'http://example.com?existing_param=Existing';

        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->request->setMethod('GET');
        $this->request->setUrl($url);
        $this->request->setRequestData($data);

        $expectedUrl = $url . '&test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($expectedUrl, $this->request->getUrl());
    }

    public function testRequestContainsNoBodyIfMethodIsGet(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->request->setMethod('GET');
        $this->request->setRequestData($data);

        $this->assertEquals('', $this->request->getBody());
    }

    public function testRequestContainsNoBodyIfMethodIsHead(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        $this->request->setMethod('HEAD');
        $this->request->setRequestData($data);

        $this->assertEquals('', $this->request->getBody());
    }

    public function testRequestContainsABodyIfMethodIsNotHeadOrGet(): void
    {
        $data = [
            'test_param1' => 'Testing1',
            'test_param2' => 'Testing2'
        ];

        
        $this->request->setMethod('POST');
        $this->request->setRequestData($data);

        $body = 'test_param1=Testing1&test_param2=Testing2';

        $this->assertEquals($body, $this->request->getBody());
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

        
        $this->request->setRequestData($data);

        $flatData = [
            'test_param1'    => 'Testing1',
            'test_param2[0]' => 'Testing2',
            'test_param2[1]' => 'Testing3'
        ];

        $this->assertEquals($flatData, $this->request->getRequestData(true));
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

        $this->request->setRequestData($data);

        $this->assertEquals($data, $this->request->getRequestData(false));
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

        
        $this->request->setHeaders($existingHeaders);
        $this->request->addHeaders($newHeaders);

        $expectedHeaders = array_merge($existingHeaders, $newHeaders);

        $this->assertEquals($expectedHeaders, $this->request->getHeaders());
    }

    public function testHeadersCanBeAccessedInJsonFormat(): void
    {
        $headers = [
            'Header1' => 'Header 1',
            'Header2' => 'Header 2'
        ];

        
        $this->request->setHeaders($headers);

        $expectedHeaders = json_encode($headers);

        $this->assertEquals($expectedHeaders, $this->request->getHeaders('json'));
    }

    public function testRawHeadersCanBeAccessed(): void
    {
        $headers = [
            'Header1' => 'Header 1',
            'Header2' => 'Header 2'
        ];

        
        $this->request->setHeaders($headers);

        $this->assertEquals($headers, $this->request->getHeaders('default'));
    }

    public function testCanAddSetting(): void
    {
        
        $this->request->addSetting('userAgent', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36');
        $this->request->addSetting('localToRemoteUrlAccessEnabled', 'true');
        $this->request->addSetting('resourceTimeout', 3000);

        $expected = [
            'userAgent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36',
            'localToRemoteUrlAccessEnabled' => 'true',
            'resourceTimeout' => 3000
        ];

        $this->assertEquals($expected, $this->request->getSettings());
    }

    public function testSetTimeoutSetsResourceTimeoutInSettings(): void
    {
        $this->request->setTimeout(1000);

        $expected = [
            'resourceTimeout' => 1000
        ];

        $this->assertEquals($expected, $this->request->getSettings());
    }

    public function testCanAddCookies(): void
    {
        $name     = 'test_cookie';
        $value    = 'TESTING_COOKIES';
        $path     = '/';
        $domain   = 'localhost';
        $httpOnly =  false;
        $secure   = true;
        $expires  = time() + 3600;

        
        $this->request->addCookie(
            $name,
            $value,
            $path,
            $domain,
            $httpOnly,
            $secure,
            $expires
        );

        $expected = [
            'name'     => $name,
            'value'    => $value,
            'path'     => $path,
            'domain'   => $domain,
            'httponly' => $httpOnly,
            'secure'   => $secure,
            'expires'  => $expires
        ];
        
        $cookies = $this->request->getCookies();
        
        $this->assertEquals([$expected], $cookies['add']);
    }

    public function testCanDeleteCookies(): void
    {
        $name     = 'test_cookie';
        $value    = 'TESTING_COOKIES';
        $path     = '/';
        $domain   = 'localhost';
        $httpOnly =  false;
        $secure   = true;
        $expires  = time() + 3600;

        
        $this->request->addCookie(
            $name,
            $value,
            $path,
            $domain,
            $httpOnly,
            $secure,
            $expires
        );

        $this->request->deleteCookie($name);

        $cookies = $this->request->getCookies();

        $this->assertEquals([$name], $cookies['delete']);
    }

    public function testCanSetViewportWidth(): void
    {
        $width  = 100;
        $height = 200;

        $this->request->setViewportSize($width, $height);

        $this->assertEquals($width, $this->request->getViewportWidth());
    }

    public function testCanSetViewportHeight(): void
    {
        $width  = 100;
        $height = 200;

        $this->request->setViewportSize($width, $height);

        $this->assertEquals($height, $this->request->getViewportHeight());
    }

}
