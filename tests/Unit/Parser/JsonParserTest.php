<?php


namespace PhantomJs\Tests\Unit\Parser;

use PhantomJs\Parser\JsonParser;
use PhantomJs\Tests\TestCase;


class JsonParserTest extends TestCase
{
    protected JsonParser $jsonParser;

    protected function setUp(): void
    {
        $this->jsonParser = new JsonParser();
    }

    public function testParseReturnsArrayIfDataIsValidJsonObject(): void
    {
        $data = '{"data": "Test data"}';

        $this->assertSame(['data' => 'Test data'], $this->jsonParser->parse($data));
    }
}
