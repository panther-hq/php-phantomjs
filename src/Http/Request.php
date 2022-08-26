<?php



namespace PhantomJs\Http;


class Request extends AbstractRequest
{
    protected ?string $type = null;

    public function getType(): string
    {
        if (!$this->type) {
            return RequestInterface::REQUEST_TYPE_DEFAULT;
        }

        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
