<?php

namespace Illuminate\Http;

class Response
{
    protected string $content;
    protected array $headers = [];

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}