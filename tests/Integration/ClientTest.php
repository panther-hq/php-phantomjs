<?php



namespace PhantomJs\Tests\Integration;

use PhantomJs\Cache\FileCache;
use PhantomJs\Client;
use PhantomJs\DependencyInjection\ServiceContainer;
use PhantomJs\Exception\RequirementException;
use PhantomJs\Exception\SyntaxException;
use PhantomJs\Tests\TestCase;
use Smalot\PdfParser\Parser;
use ZendPdf\PdfDocument;

class ClientTest extends TestCase
{

    protected FileCache $fileCache;

    protected Client $client;

    public function setUp(): void
    {
        $this->fileCache = new FileCache();
        $serviceContainer = ServiceContainer::getInstance();

        $this->client = new Client(
            $serviceContainer->get('engine'),
            $serviceContainer->get('procedure_loader'),
            $serviceContainer->get('procedure_compiler'),
            $serviceContainer->get('message_factory')
        );

    }

    public function testAdditionalProceduresCanBeLoadedThroughChainLoader(): void
    {
        $content = 'TEST_PROCEDURE';

        $procedure = <<<EOF
    console.log(JSON.stringify({"content": "$content"}, undefined, 4));
    phantom.exit(1);
EOF;

        $this->fileCache->save('key', $procedure);

        $procedureLoaderFactory = $this->getContainer()->get('procedure_loader_factory');
        $procedureLoader = $procedureLoaderFactory->createProcedureLoader(sys_get_temp_dir());

        $this->client->setProcedure('key');
        $this->client->getProcedureLoader()->addLoader($procedureLoader);

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $this->client->send($request, $response);

        $this->assertSame($content, $response->getContent());
    }

    public function testAdditionalProceduresCanBeLoadedThroughChainLoaderIfProceduresContainComments(): void
    {
        $content = 'TEST_PROCEDURE';

        $procedure = <<<EOF
    console.log(JSON.stringify({"content": "$content"}, undefined, 4));
    phantom.exit(1);
    var test = function () {
        // Test comment
        console.log('test');
    };
EOF;

        $this->fileCache->save('key', $procedure);

        $procedureLoaderFactory = $this->getContainer()->get('procedure_loader_factory');
        $procedureLoader = $procedureLoaderFactory->createProcedureLoader(sys_get_temp_dir());

        $this->client->setProcedure('key');
        $this->client->getProcedureLoader()->addLoader($procedureLoader);

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $this->client->send($request, $response);

        $this->assertSame($content, $response->getContent());
    }

    public function testSyntaxExceptionIsThrownIfRequestProcedureContainsSyntaxError(): void
    {
        $this->expectException(RequirementException::class);

        $procedure = <<<EOF
    console.log(;
EOF;

        $this->fileCache->save('key', $procedure);

        $procedureLoaderFactory = $this->getContainer()->get('procedure_loader_factory');
        $procedureLoader = $procedureLoaderFactory->createProcedureLoader(sys_get_temp_dir());

        $this->client->setProcedure('test');
        $this->client->getProcedureLoader()->addLoader($procedureLoader);

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $this->client->send($request, $response);
    }


    public function testCanSetUserAgentInSettings(): void
    {
        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->addSetting('userAgent', 'PhantomJS TEST');

        $this->client->send($request, $response);

        $this->assertContains('PhantomJS TEST', $request->getSettings());
        $this->assertArrayHasKey('userAgent', $request->getSettings());
    }

    public function testCanAddCookiesToRequest(): void
    {
        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->addCookie('test_cookie', 'TESTING_COOKIES', '/', '.jonnyw.kiwi');

        $this->client->send($request, $response);

        $this->assertContains('TESTING_COOKIES', current($response->getCookies()));
    }

    public function testCanLoadCookiesFromPersistentCookieFile(): void
    {
        $file = sys_get_temp_dir().'/cookies.txt';
        
        $this->client->getEngine()->addOption('--cookies-file='.$file);

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $expireAt = strtotime('16-Nov-2020 00:00:00');

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->addCookie('test_cookie', 'TESTING_COOKIES', '/', '.jonnyw.kiwi', true, false, ($expireAt * 1000));

        $this->client->send($request, $response);
        $this->assertStringContainsString("QNetworkCookie", file_get_contents($file));
    }
    

    public function testCanDeleteAllCookiesFromPersistentCookieFile(): void
    {
        $file = sys_get_temp_dir().'/cookies.txt';
        
        $this->client->getEngine()->addOption('--cookies-file='.$file);

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $expireAt = strtotime('16-Nov-2020 00:00:00');

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->addCookie('test_cookie_1', 'TESTING_COOKIES_1', '/', '.jonnyw.kiwi', true, false, ($expireAt * 1000));
        $request->addCookie('test_cookie_2', 'TESTING_COOKIES_2', '/', '.jonnyw.kiwi', true, false, ($expireAt * 1000));

        $this->client->send($request, $response);

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->deleteCookie('*');

        $this->client->send($request, $response);

        $this->assertStringNotContainsString('test_cookie_1=TESTING_COOKIES_1; HttpOnly; expires=Mon, 16-Nov-2020 00:00:00 GMT; domain=.jonnyw.kiwi; path=/)', file_get_contents($file));
        $this->assertStringNotContainsString('test_cookie_2=TESTING_COOKIES_2; HttpOnly; expires=Mon, 16-Nov-2020 00:00:00 GMT; domain=.jonnyw.kiwi; path=/)', file_get_contents($file));
    }

    public function testCookiesPresentInResponse(): void
    {
        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $expireAt = strtotime('2025-01-01');

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->addCookie('test_cookie', 'TESTING_COOKIES', '/', '.jonnyw.kiwi', true, false, $expireAt);

        $this->client->send($request, $response);

        $cookies = $response->getCookies();
        dd($cookies);
        $this->assertEquals([
            'domain' => '.jonnyw.kiwi',
            'expires' => 'Mon, 16 Nov 2020 00:00:00 GMT',
            'expiry' => '1605484800',
            'httponly' => true,
            'name' => 'test_cookie',
            'path' => '/',
            'secure' => false,
            'value' => 'TESTING_COOKIES',
        ], $cookies[0]);
    }

    public function testResponseContainsConsoleErrorIfAJavascriptErrorExistsOnThePage(): void
    {
        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-console-error');

        $this->client->send($request, $response);

        $console = $response->getConsole();

        $this->assertCount(1, $console);
        $this->assertContains('ReferenceError: Can\'t find variable: invalid', $console[0]['message']);
    }

    public function testResponseContainsConsoleTraceIfAJavascriptErrorExistsOnThePage(): void
    {
        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-console-error');

        $this->client->send($request, $response);

        $console = $response->getConsole();

        $this->assertCount(1, $console[0]['trace']);
    }

    public function testResponseContainsHeaders(): void
    {
        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-console-error');

        $this->client->send($request, $response);

        $this->assertNotEmpty($response->getHeaders());
    }
    
    public function testRedirectUrlIsSetInResponseIfRequestIsRedirected(): void
    {
        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('https://jigsaw.w3.org/HTTP/300/302.html');

        $this->client->send($request, $response);

        $this->assertNotEmpty($response->getRedirectUrl());
    }

    public function testPostRequestSendsRequestData(): void
    {
        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('POST');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-post');
        $request->setRequestData([
            'test1' => 'http://test.com',
            'test2' => 'A string with an \' ) / # some other invalid [ characters.',
        ]);

        $this->client->send($request, $response);

        $this->assertStringContainsString(sprintf('<li>test1=%s</li>', 'http://test.com'), $response->getContent());
        $this->assertStringContainsString(sprintf('<li>test2=%s</li>', 'A string with an \' ) / # some other invalid [ characters.'), $response->getContent());
    }
    
    public function testCaptureRequestSavesFileToLocalDisk(): void
    {
        $file = sys_get_temp_dir().'/test.jpg';

        $request = $this->client->getMessageFactory()->createCaptureRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setOutputFile($file);

        $this->client->send($request, $response);

        $this->assertFileExists($file);
    }

    public function testCaptureRequestSavesFileToDiskWithCorrectCaptureDimensions(): void
    {
        $file = sys_get_temp_dir().'/test.jpg';

        $width = 200;
        $height = 400;
        
        $request = $this->client->getMessageFactory()->createCaptureRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setOutputFile($file);
        $request->setCaptureDimensions($width, $height);

        $this->client->send($request, $response);

        $imageInfo = getimagesize($file);

        $this->assertEquals($width, $imageInfo[0]);
        $this->assertEquals($height, $imageInfo[1]);
    }

    public function testPdfRequestSavesPdfToLocalDisk(): void
    {
        $file = sys_get_temp_dir().'/test.pdf';

        $request = $this->client->getMessageFactory()->createPdfRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setOutputFile($file);

        $this->client->send($request, $response);

        $this->assertFileExists($file);
    }

    public function testPdfRequestSavesFileToDiskWithCorrectPaperSize(): void
    {
        $file = sys_get_temp_dir().'/test.pdf';

        $width = 20;
        $height = 30;

        $request = $this->client->getMessageFactory()->createPdfRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setOutputFile($file);
        $request->setPaperSize(sprintf('%scm', $width), sprintf('%scm', $height));
        $request->setMargin('0cm');

        $this->client->send($request, $response);

        $pdf = PdfDocument::load($file);

        $pdfWidth = round(($pdf->pages[0]->getWidth() * 0.0352777778));
        $pdfHeight = round(($pdf->pages[0]->getHeight() * 0.0352777778));

        $this->assertEquals($width, $pdfWidth);
        $this->assertEquals($height, $pdfHeight);
    }

    public function testPdfRequestSavesFileToDiskWithCorrectFormatSize(): void
    {
        $file = sys_get_temp_dir().'/test.pdf';

        $request = $this->client->getMessageFactory()->createPdfRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setOutputFile($file);
        $request->setFormat('A4');
        $request->setMargin('0cm');

        $this->client->send($request, $response);

        $pdf = PdfDocument::load($file);

        $pdfWidth = round(($pdf->pages[0]->getWidth() * 0.0352777778));
        $pdfHeight = round(($pdf->pages[0]->getHeight() * 0.0352777778));

        $this->assertEquals(21, $pdfWidth);
        $this->assertEquals(30, $pdfHeight);
    }

    public function testPdfRequestSavesFileToDiskWithCorrectOrientation(): void
    {
        $file = sys_get_temp_dir().'/test.pdf';

        $request = $this->client->getMessageFactory()->createPdfRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setOutputFile($file);
        $request->setFormat('A4');
        $request->setOrientation('landscape');
        $request->setMargin('0cm');

        $this->client->send($request, $response);

        $pdf = PdfDocument::load($file);

        $pdfWidth = round(($pdf->pages[0]->getWidth() * 0.0352777778));
        $pdfHeight = round(($pdf->pages[0]->getHeight() * 0.0352777778));

        $this->assertEquals(30, $pdfWidth);
        $this->assertEquals(21, $pdfHeight);
    }

    public function testCanSetRepeatingHeaderForPDFRequest(): void
    {
        $file = sys_get_temp_dir().'/test.pdf';

        $request = $this->client->getMessageFactory()->createPdfRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setOutputFile($file);
        $request->setFormat('A4');
        $request->setOrientation('landscape');
        $request->setMargin('0cm');
        $request->setRepeatingHeader('<h1>Header <span style="float:right">%pageNum% / %pageTotal%</span></h1>', '2cm');
        $request->setRepeatingFooter('<footer>Footer <span style="float:right">%pageNum% / %pageTotal%</span></footer>', '2cm');

        $this->client->send($request, $response);

        $parser = new Parser();
        $pdf = $parser->parseFile($file);

        $text = str_replace(' ', '', $pdf->getText());

        $this->assertContains('Header', $text);
    }

    public function testCanSetRepeatingFooterForPDFRequest(): void
    {
        $file = sys_get_temp_dir().'/test.pdf';

        $request = $this->client->getMessageFactory()->createPdfRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setOutputFile($file);
        $request->setFormat('A4');
        $request->setOrientation('landscape');
        $request->setMargin('0cm');
        $request->setRepeatingHeader('<h1>Header <span style="float:right">%pageNum% / %pageTotal%</span></h1>', '2cm');
        $request->setRepeatingFooter('<footer>Footer <span style="float:right">%pageNum% / %pageTotal%</span></footer>', '2cm');

        $this->client->send($request, $response);

        $parser = new Parser();
        $pdf = $parser->parseFile($file);

        $text = str_replace(' ', '', $pdf->getText());

        $this->assertContains('Footer', $text);
    }

    public function testSetViewportSizeSetsSizeOfViewportInDefaultRequest(): void
    {
        $width = 100;
        $height = 200;

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->setViewportsize($width, $height);

        $this->client->send($request, $response);

        $logs = explode("\n", $this->client->getLog());

        $startIndex = $this->getLogEntryIndex($logs, 'Set viewport size ~ width: 100 height: 200');

        $this->assertTrue((false !== $startIndex));
    }

    public function testSetViewportSizeSetsSizeOfViewportInCaptureRequest(): void
    {
        $width = 100;
        $height = 200;

        $request = $this->client->getMessageFactory()->createCaptureRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->setViewportsize($width, $height);

        $this->client->send($request, $response);

        $logs = explode("\n", $this->client->getLog());

        $startIndex = $this->getLogEntryIndex($logs, 'Set viewport size ~ width: 100 height: 200');

        $this->assertTrue((false !== $startIndex));
    }

    public function testDelayLogsStartTimeInClientForDefaultRequest(): void
    {
        $delay = 1;

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->setDelay($delay);

        $this->client->send($request, $response);

        $logs = explode("\n", $this->client->getLog());

        $startIndex = $this->getLogEntryIndex($logs, 'Delaying page render for');

        $this->assertTrue((false !== $startIndex));
    }

    public function testDelayLogsEndTimeInClientForDefaultRequest(): void
    {
        $delay = 1;

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->setDelay($delay);

        $this->client->send($request, $response);

        $logs = explode("\n", $this->client->getLog());

        $endIndex = $this->getLogEntryIndex($logs, 'Rendering page after');

        $this->assertTrue((false !== $endIndex));
    }

    public function testDelayDelaysPageRenderForSpecifiedTimeForDefaultRequest(): void
    {
        $delay = 1;

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');
        $request->setDelay($delay);

        $this->client->send($request, $response);

        $logs = explode('\\n', $this->client->getLog());

        $startIndex = $this->getLogEntryIndex($logs, 'Delaying page render for');
        $endIndex = $this->getLogEntryIndex($logs, 'Rendering page after');

        $startTime = strtotime(substr($logs[$startIndex], 0, 19));
        $endTime = strtotime(substr($logs[$endIndex], 0, 19));

        $this->assertSame(($startTime + $delay), $endTime);
    }

    public function testDelayLogsStartTimeInClientForCaptureRequest(): void
    {
        $delay = 1;

        $request = $this->client->getMessageFactory()->createCaptureRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setDelay($delay);

        $this->client->send($request, $response);

        $logs = explode('\\n', $this->client->getLog());

        $startIndex = $this->getLogEntryIndex($logs, 'Delaying page render for');

        $this->assertTrue((false !== $startIndex));
    }

    public function testDelayLogsEndTimeInClientForCaptureRequest(): void
    {
        $delay = 1;

        $request = $this->client->getMessageFactory()->createCaptureRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setDelay($delay);

        $this->client->send($request, $response);

        $logs = explode('\\n', $this->client->getLog());

        $endIndex = $this->getLogEntryIndex($logs, 'Rendering page after');

        $this->assertTrue((false !== $endIndex));
    }

    public function testDelayDelaysPageRenderForSpecifiedTimeForCaptureRequest(): void
    {
        $delay = 1;

        $request = $this->client->getMessageFactory()->createCaptureRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setDelay($delay);

        $this->client->send($request, $response);

        $logs = explode('\\n', $this->client->getLog());

        $startIndex = $this->getLogEntryIndex($logs, 'Delaying page render for');
        $endIndex = $this->getLogEntryIndex($logs, 'Rendering page after');

        $startTime = strtotime(substr($logs[$startIndex], 0, 19));
        $endTime = strtotime(substr($logs[$endIndex], 0, 19));

        $this->assertSame(($startTime + $delay), $endTime);
    }

    public function testLazyRequestReturnsResourcesAfterAllResourcesAreLoaded(): void
    {
        $this->client->isLazy();

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-lazy');
        $request->setTimeout(5000);

        $this->client->send($request, $response);

        $this->assertStringContainsString('<p id="content">loaded</p>', $response->getContent());
    }

    public function testContentIsReturnedForLazyRequestIfTimeoutIsReachedBeforeResourceIsLoaded(): void
    {
        $this->client->isLazy();

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-lazy');
        $request->setTimeout(1000);

        $this->client->send($request, $response);

        $this->assertStringContainsString('<p id="content"></p>', $response->getContent());
    }

    public function testDebugLogsDebugInfoToClientLog(): void
    {
        $this->client->getEngine()->debug(true);

        $request = $this->client->getMessageFactory()->createRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-default');

        $this->client->send($request, $response);

        $this->assertStringContainsString('[DEBUG]', $this->client->getLog());
    }

    public function testCanSetPageBackgroundColor(): void
    {
        $file = sys_get_temp_dir().'/test.jpg';

        $request = $this->client->getMessageFactory()->createCaptureRequest('http://example.com');
        $response = $this->client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('http://www.jonnyw.kiwi/tests/test-capture');
        $request->setBodyStyles(['backgroundColor' => 'red']);
        $request->setOutputFile($file);

        $this->client->send($request, $response);

        $this->assertStringContainsString('body style="background-color: red;"', $response->getContent());
    }

    private function getLogEntryIndex(array $logs, $search)
    {
        foreach ($logs as $index => $log) {
            $pos = stripos($log, $search);

            if (false !== $pos) {
                return $index;
            }
        }

        return false;
    }
}
