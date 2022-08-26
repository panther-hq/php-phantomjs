<?php


namespace PhantomJs\Procedure;

use PhantomJs\Validator\EngineInterface;
use PhantomJs\Exception\SyntaxException;
use PhantomJs\Exception\RequirementException;


class ProcedureValidator implements ProcedureValidatorInterface
{

    public function __construct(protected ProcedureLoaderInterface $procedureLoader,protected EngineInterface $engine)
    {
    }

    public function validate(string $procedure): bool
    {
        $this->validateSyntax($procedure);
        $this->validateRequirements($procedure);

        return true;
    }

    protected function validateSyntax(string $procedure): void
    {
        $input  = new Input();
        $output = new Output();

        $input->set('procedure', $procedure);
        $input->set('engine', $this->engine->toString());

        $validator = $this->procedureLoader->load('validator');
        $validator->run($input, $output);

        $errors = $output->get('errors');

        if (!empty($errors)) {
            throw new SyntaxException('Your procedure failed to compile due to a javascript syntax error', (array) $errors);
        }
    }

    protected function validateRequirements(string $procedure): void
    {
        if (preg_match('/phantom\.exit\(/', $procedure) !== 1) {
            throw new RequirementException('Your procedure must contain a \'phantom.exit(1);\' command to avoid the PhantomJS process hanging');
        }
    }
}
