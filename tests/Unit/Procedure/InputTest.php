<?php


namespace PhantomJs\Tests\Unit\Procedure;

use PhantomJs\Procedure\Input;
use PhantomJs\Procedure\InputInterface;
use PhantomJs\Tests\TestCase;


class InputTest extends TestCase
{
    protected InputInterface $input;

    protected function setUp(): void
    {
        $this->input = new Input();
    }

    public function testDataStorage(): void
    {
        $this->input->set('test', 'Test value');

        $this->assertSame('Test value', $this->input->get('test'));
    }

}
