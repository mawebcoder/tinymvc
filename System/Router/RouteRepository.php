<?php

namespace System\Router;

class RouteRepository
{
    public HttpVerbsEnum $httpVerb;
    public string $uri;
    public array $action;

    public ?string $name = null;

    public array $middlewares = [];

    public function setUri(string $uri): static
    {
        $this->uri = $uri;
        return $this;
    }

    public function setAction(array $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setMiddlewares(array $middlewares): static
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function setHttpVerb(HttpVerbsEnum $httpVerb): static
    {
        $this->httpVerb = $httpVerb;

        return $this;
    }

    public function getUri(): string
    {
        return  $this->uri;
    }

    public function getAction(): array
    {
        return  $this->action;
    }
}