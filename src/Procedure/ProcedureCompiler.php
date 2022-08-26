<?php



namespace PhantomJs\Procedure;

use PhantomJs\Cache\CacheInterface;
use PhantomJs\Template\TemplateRendererInterface;


class ProcedureCompiler implements ProcedureCompilerInterface
{
    protected bool $cacheEnabled = true;

    public function __construct(
        protected ProcedureLoaderInterface $procedureLoader,
        protected ProcedureValidatorInterface $procedureValidator,
        protected CacheInterface $cacheHandler,
        protected TemplateRendererInterface $renderer
    )
    {
    }

    public function compile(ProcedureInterface $procedure, InputInterface $input): void
    {
        $cacheKey = sprintf('phantomjs_%s_%s', $input->getType(), md5($procedure->getTemplate()));

        if ($this->cacheEnabled && $this->cacheHandler->exists($cacheKey)) {
            $template = $this->cacheHandler->fetch($cacheKey);
        }

        if (empty($template)) {

            $template  = $this->renderer
                ->render($procedure->getTemplate(), ['engine' => $this, 'procedure_type' => $input->getType()]);

            $test = clone $procedure;
            $test->setTemplate($template);

            $compiled = $test->compile($input);

            $this->procedureValidator->validate($compiled);

            if ($this->cacheEnabled) {
                $this->cacheHandler->save($cacheKey, $template);
            }
        }

        $procedure->setTemplate($template);
    }

    public function load(string $name): string
    {
        return $this->procedureLoader->loadTemplate($name);
    }

    public function enableCache(): void
    {
        $this->cacheEnabled = true;
    }

    public function disableCache(): void
    {
        $this->cacheEnabled = false;
    }

    public function clearCache(): void
    {
        $this->cacheHandler->delete('phantomjs_*');
    }
}
