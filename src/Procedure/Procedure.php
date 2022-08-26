<?php


namespace PhantomJs\Procedure;

use PhantomJs\Engine;
use PhantomJs\Cache\CacheInterface;
use PhantomJs\Parser\ParserInterface;
use PhantomJs\Template\TemplateRendererInterface;
use PhantomJs\Exception\NotWritableException;
use PhantomJs\Exception\ProcedureFailedException;
use PhantomJs\StringUtils;


class Procedure implements ProcedureInterface
{
    protected ?string $template = null;

    public function __construct(
        protected Engine                    $engine,
        protected ParserInterface           $parser,
        protected CacheInterface            $cache,
        protected TemplateRendererInterface $renderer
    )
    {
    }

    public function run(InputInterface $input, OutputInterface $output): void
    {
        $key = StringUtils::random(20);

        try {
            $this->cache->save($key, $this->compile($input));

            $descriptorspec = [
                ['pipe', 'r'],
                ['pipe', 'w'],
                ['pipe', 'w']
            ];

            $process = proc_open(escapeshellcmd(sprintf('%s %s', $this->engine->getCommand(), $this->cache->getPath($key))), $descriptorspec, $pipes, null, null);

            if (!is_resource($process)) {
                throw new ProcedureFailedException('proc_open() did not return a resource');
            }

            $result = stream_get_contents($pipes[1]);
            $log = stream_get_contents($pipes[2]);

            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            proc_close($process);

            if (!empty($result)){
                $output->import($this->parser->parse($result));
            }

            $this->engine->log($log);

            $this->cache->delete($key);

        } catch (NotWritableException $e) {
            throw $e;
        } catch (\Exception $e) {

            if (isset($executable)) {
                $this->cache->delete($key);
            }

            throw new ProcedureFailedException(sprintf('Error when executing PhantomJs procedure - %s', $e->getMessage()));
        }
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function compile(InputInterface $input): string
    {
        return $this->renderer->render($this->getTemplate(), array('input' => $input));
    }

}
