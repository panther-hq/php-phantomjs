<?php


namespace PhantomJs\Tests\Unit\Procedure;

use PhantomJs\Procedure\Output;
use PhantomJs\Procedure\OutputInterface;
use PhantomJs\Tests\TestCase;


class OutputTest extends TestCase
{
    protected OutputInterface $output;

    protected function setUp(): void
    {
        $this->output = new Output();
    }

    public function testDataStorage(): void
    {
        $this->output->set('test', 'Test value');

        $this->assertSame('Test value', $this->output->get('test'));
    }

    public function testCanImportData(): void
    {
        $data = [
            'test' => 'Test value',
            'test2' => 'Test value 2'
        ];

        $this->output->import($data);

        $this->assertSame('Test value', $this->output->get('test'));
    }

    public function testCanLogData(): void
    {
        $this->output->log('Test log');

        $this->assertContains('Test log', $this->output->getLogs());
    }

}
