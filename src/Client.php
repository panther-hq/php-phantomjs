<?php

namespace PhantomJs;

use PhantomJs\Http\CaptureRequestInterface;
use PhantomJs\Http\PdfRequestInterface;
use PhantomJs\Procedure\InputInterface;
use PhantomJs\Procedure\OutputInterface;
use PhantomJs\Procedure\ProcedureLoaderInterface;
use PhantomJs\Procedure\ProcedureCompilerInterface;
use PhantomJs\Http\MessageFactoryInterface;
use PhantomJs\Http\RequestInterface;
use PhantomJs\Http\ResponseInterface;
use PhantomJs\DependencyInjection\ServiceContainer;

class Client implements ClientInterface
{
    private static ?ClientInterface $instance = null;

    protected string $procedure;


    public function __construct(
        protected Engine                     $engine,
        protected ProcedureLoaderInterface   $procedureLoader,
        protected ProcedureCompilerInterface $procedureCompiler,
        protected MessageFactoryInterface    $messageFactory
    )
    {
        $this->procedure = 'http_default';
    }

    public static function getInstance(): ClientInterface
    {
        if (!self::$instance instanceof ClientInterface) {

            $serviceContainer = ServiceContainer::getInstance();

            self::$instance = new static(
                $serviceContainer->get('engine'),
                $serviceContainer->get('procedure_loader'),
                $serviceContainer->get('procedure_compiler'),
                $serviceContainer->get('message_factory')
            );
        }

        return self::$instance;
    }

    public function getEngine(): Engine
    {
        return $this->engine;
    }

    public function getMessageFactory(): MessageFactoryInterface
    {
        return $this->messageFactory;
    }

    public function getProcedureLoader(): ProcedureLoaderInterface
    {
        return $this->procedureLoader;
    }

    public function send(RequestInterface|CaptureRequestInterface|PdfRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $procedure = $this->procedureLoader->load($this->procedure);

        if ($request instanceof InputInterface){
            $this->procedureCompiler->compile($procedure, $request);

            if ($response instanceof OutputInterface){
                $procedure->run($request, $response);
            }
        }


        return $response;
    }

    public function getLog(): string
    {
        return $this->getEngine()->getLog();
    }

    public function setProcedure(string $procedure): void
    {
        $this->procedure = $procedure;
    }

    public function getProcedure(): string
    {
        return $this->procedure;
    }

    public function getProcedureCompiler(): ProcedureCompilerInterface
    {
        return $this->procedureCompiler;
    }

    public function isLazy(): void
    {
        $this->procedure = 'http_lazy';
    }
}
