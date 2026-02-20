<?php

namespace System\Router;

use System\Exceptions\RouteNotFoundException;
use System\Exceptions\HttpVerbIsNotValidException;

class Routing
{
    private ?string $currentUrl = null;
    private ?string $currentUri = null;
    private array $queryParam = [];

    public ?HttpVerbsEnum $httpVerb = null;


    private array $routes = [];

    /**
     * @throws HttpVerbIsNotValidException
     */
    public function __construct()
    {
        $this->parseUrl();

        $this->initializeRoutes();
    }

    /**
     * @return void
     * @throws HttpVerbIsNotValidException
     */
    private function parseUrl(): void
    {
        $this->currentUrl = trim($_SERVER['REQUEST_URI'], '/') !== '/' ? trim($_SERVER['REQUEST_URI']) : null;

        if ($this->currentUrl) {
            $this->currentUri = trim(parse_url($this->currentUrl)['path'], '/');

            parse_str(parse_url($this->currentUrl)['query'] ?? '', $this->queryParam);
        }

        $this->parseHttpVerb();
    }

    private function initializeRoutes(): void
    {
        $this->routes[HttpVerbsEnum::GET->value] = [];
        $this->routes[HttpVerbsEnum::PATCH->value] = [];
        $this->routes[HttpVerbsEnum::POST->value] = [];
        $this->routes[HttpVerbsEnum::OPTIONS->value] = [];
        $this->routes[HttpVerbsEnum::PUT->value] = [];
        $this->routes[HttpVerbsEnum::DELETE->value] = [];
        $this->routes[HttpVerbsEnum::HEAD->value] = [];
    }

    public function getCurrentUrl(): ?string
    {
        return $this->currentUrl ?? null;
    }

    public function getQueryParam(): array
    {
        return $this->queryParam;
    }

    public function get(string $uri, string|array $action): static
    {
        $this->routes[HttpVerbsEnum::GET->value][trim($uri, '/')] = $action;
        return $this;
    }

    public function post(string $uri, string|array $action): static
    {
        $this->routes[HttpVerbsEnum::POST->value][trim($uri, '/')] = $action;
        return $this;
    }

    public function put(string $uri, string|array $action): static
    {
        $this->routes[HttpVerbsEnum::PUT->value][trim($uri, '/')] = $action;
        return $this;
    }

    public function delete(string $uri, string|array $action): static
    {
        $this->routes[HttpVerbsEnum::DELETE->value][trim($uri, '/')] = $action;
        return $this;
    }

    public function patch(string $uri, string|array $action): static
    {
        $this->routes[HttpVerbsEnum::PATCH->value][trim($uri, '/')] = $action;
        return $this;
    }


    /**
     * @throws HttpVerbIsNotValidException
     */
    private function parseHttpVerb(): void
    {
        $this->httpVerb = HttpVerbsEnum::tryFrom($_SERVER['REQUEST_METHOD']);

        if (!$this->httpVerb) {
            throw new HttpVerbIsNotValidException('Http Verb is not valid');
        }
    }


    public function dispatchRoute(): void
    {
        foreach ($this->routes[$this->httpVerb->value] as $route => $callback) {
            $pattern = preg_replace('/\{[a-zA-Z_]+}/', '([a-zA-Z0-9-_]+)', $route);

            $pattern = "#^$pattern$#";

            if (preg_match($pattern, $this->currentUri, $matches)) {
                array_shift($matches);

                if (is_array($callback)) {
                    [0 => $controller, 1 => $method] = $callback;

                    //or
//                    list($controller, $method) = $callback;

                    call_user_func_array([new $controller, $method], $matches);
                }
                return;
            }
        }

        throw new RouteNotFoundException('Route ' . $this->currentUri . ' not found');
    }


    public function getArgumentName(string $pathParameter): string
    {
        $startingString = strpos($pathParameter, '{');

        $startingString += strlen('{');

        $size = strpos($pathParameter, '}', $startingString) - $startingString;

        return substr($pathParameter, $startingString, $size);
    }

    private function isPathParameter(string $uriSegment): bool
    {
        return preg_match('/^{[A-z]*}$/', $uriSegment);
    }


}