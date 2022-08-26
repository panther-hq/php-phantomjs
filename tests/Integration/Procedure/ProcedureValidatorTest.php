<?php


namespace PhantomJs\Tests\Integration\Procedure;

use PhantomJs\Exception\RequirementException;
use PhantomJs\Exception\SyntaxException;
use PhantomJs\Tests\TestCase;
use Symfony\Component\Config\FileLocator;
use PhantomJs\Client;
use PhantomJs\Procedure\ProcedureLoaderInterface;
use PhantomJs\Procedure\ProcedureValidator;
use PhantomJs\Validator\Esprima;
use PhantomJs\Validator\EngineInterface;


class ProcedureValidatorTest extends TestCase
{
    protected ProcedureLoaderInterface $procedureLoader;
    protected Esprima $esprima;
    
    protected function setUp(): void
    {
        $this->procedureLoader = Client::getInstance()->getProcedureLoader();
        $this->esprima = new Esprima(new FileLocator(realpath(sprintf('%s/../../../src/Resources/validators/', __DIR__))), 'esprima-2.0.0.js');
    }


    public function testRequirementExceptionIsThrownIfProcedureDoesNotContainPhanomtExitStatement(): void
    {
        $this->expectException(RequirementException::class);

        $validator = new ProcedureValidator($this->procedureLoader, $this->esprima);
        $validator->validate('var test = function () { console.log("ok"); }');
    }

    public function testTrueIsReturnedIfProcedureIsValid(): void
    {
        $validator = new ProcedureValidator($this->procedureLoader, $this->esprima);

        $this->assertTrue($validator->validate('var test = function () { console.log("ok"); }; phantom.exit(1);'));
    }

    public function testProcedureIsValidIfProcedureHasComments(): void
    {
        $validator = new ProcedureValidator($this->procedureLoader, $this->esprima);

        $this->assertTrue($validator->validate('/** * Test comment **/ var test = function () { console.log("ok"); }; phantom.exit(1);'));
    }

}
