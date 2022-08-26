<?php


namespace PhantomJs\Tests\Unit;

use PhantomJs\StringUtils;
use PhantomJs\Tests\TestCase;


class StringUtilsTest extends TestCase
{
    public function testCanGenerateRandomStringForSpecificLength(): void
    {
        $string = StringUtils::random(14);

        $this->assertEquals(14, strlen($string));
    }

    public function testRandomStringIsRandom(): void
    {
        $string1 = StringUtils::random(14);
        $string2 = StringUtils::random(14);

        $this->assertNotEquals($string1, $string2);
    }
}
