<?php


namespace PhantomJs\Http;

use PhantomJs\Exception\InvalidMethodException;
use PhantomJs\Procedure\InputInterface;


abstract class AbstractRequest implements RequestInterface, InputInterface
{
    protected array $headers =[];
    protected array $settings = [];
    protected array $cookies = [
        'add' => [],
        'delete' => []
    ];
    protected array $data = [];
    protected ?string $url = null;
    protected string $method;
    protected int $timeout;
    protected int $delay = 0;
    protected int $viewportWidth = 0;
    protected int $viewportHeight = 0;
    protected array $bodyStyles = [];

    public function __construct(string $url, string $method = RequestInterface::METHOD_GET, int $timeout = 5000)
    {
        $this->setMethod($method);
        $this->setTimeout($timeout);
        $this->setUrl($url);
    }

    public function setMethod(string $method): self
    {
        $method = strtoupper($method);
        $reflection = new \ReflectionClass(RequestInterface::class);

        if (!$reflection->hasConstant('METHOD_' . $method)) {
            throw new InvalidMethodException(sprintf('Invalid method provided: %s', $method));
        }

        $this->method = $method;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setTimeout(int $timeout): self
    {
        $this->settings['resourceTimeout'] = $timeout;

        return $this;
    }

    public function getTimeout(): ?int
    {
        if (isset($this->settings['resourceTimeout'])) {
            return $this->settings['resourceTimeout'];
        }

        return null;
    }

    public function setDelay(int $delay): self
    {
        $this->delay = $delay;

        return $this;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    public function setViewportSize(int $width, int $height): self
    {
        $this->viewportWidth = $width;
        $this->viewportHeight = $height;

        return $this;
    }

    public function getViewportWidth(): int
    {
        return $this->viewportWidth;
    }

    public function getViewportHeight(): int
    {
        return $this->viewportHeight;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        if (!in_array($this->getMethod(), [RequestInterface::METHOD_GET, RequestInterface::METHOD_HEAD])) {
            return $this->url;
        }

        $url = $this->url;

        if (count($this->data)) {
            $url .= !str_contains($url, '?') ? '?' : '&';
            $url .= http_build_query($this->data);
        }

        return $url;
    }

    public function getBody(): string
    {
        if (in_array($this->getMethod(), [RequestInterface::METHOD_GET, RequestInterface::METHOD_HEAD])) {
            return '';
        }

        return http_build_query($this->getRequestData());
    }

    public function setRequestData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getRequestData(bool $flat = true): array
    {
        if ($flat) {
            return $this->flattenData($this->data);
        }

        return $this->data;
    }


    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function addHeader(string $header, string $value): self
    {
        $this->headers[$header] = $value;

        return $this;
    }

    public function addHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function getHeaders(string $format = 'default'): array|string
    {
        if ($format === 'json') {
            return json_encode($this->headers);
        }

        return $this->headers;
    }

    public function addSetting(string $setting, string $value): self
    {
        $this->settings[$setting] = $value;

        return $this;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function addCookie(
        string $name,
        mixed $value,
        string $path,
        string $domain,
        bool $httpOnly = true,
        bool $secure = false,
        int $expires = null
    ): self
    {
        $filter = function ($value) {
            return !is_null($value);
        };

        $this->cookies['add'][] = array_filter([
            'name' => $name,
            'value' => $value,
            'path' => $path,
            'domain' => $domain,
            'httponly' => $httpOnly,
            'secure' => $secure,
            'expires' => $expires
        ], $filter);

        return $this;
    }

    public function deleteCookie(string $name): self
    {
        $this->cookies['delete'][] = $name;

        return $this;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function setBodyStyles(array $styles): self
    {
        $this->bodyStyles = $styles;

        return $this;
    }

    public function getBodyStyles(string $format = 'default'): array|string
    {
        if ($format === 'json') {
            return json_encode($this->bodyStyles);
        }

        return $this->bodyStyles;
    }

    protected function flattenData(array $data, string $prefix = '', string $format = '%s'): array
    {
        $flat = [];

        foreach ($data as $name => $value) {

            $ref = $prefix . sprintf($format, $name);

            if (is_array($value)) {

                $flat += $this->flattenData($value, $ref, '[%s]');
                continue;
            }

            $flat[$ref] = $value;
        }

        return $flat;
    }
}
