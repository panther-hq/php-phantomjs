<?php



namespace PhantomJs\Procedure;

use PhantomJs\Engine;
use PhantomJs\Cache\CacheInterface;
use PhantomJs\Parser\ParserInterface;
use PhantomJs\Template\TemplateRendererInterface;


class ProcedureFactory implements ProcedureFactoryInterface
{
    public function __construct(
        protected Engine $engine,
        protected ParserInterface $parser,
        protected CacheInterface $cacheHandler,
        protected TemplateRendererInterface $renderer
    )
    {
    }

    public function createProcedure(): ProcedureInterface
    {
        return new Procedure(
            $this->engine,
            $this->parser,
            $this->cacheHandler,
            $this->renderer
        );
    }
}
