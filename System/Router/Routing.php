<?php

namespace System\Router;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use System\Helper\Helper;
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

    public function get(string $uri, array $action): RouteRepository
    {
        $routeRepository = new RouteRepository()
            ->setUri(trim($uri, '/'))
            ->setHttpVerb(HttpVerbsEnum::GET)
            ->setAction($action);

        $this->routes[HttpVerbsEnum::GET->value][] = $routeRepository;


        return $this->routes[HttpVerbsEnum::GET->value][array_key_last($this->routes[HttpVerbsEnum::GET->value])];
    }

    public function post(string $uri, array $action): RouteRepository
    {
        $routeRepository = new RouteRepository()
            ->setUri(trim($uri, '/'))
            ->setHttpVerb(HttpVerbsEnum::POST)
            ->setAction($action);

        $this->routes[HttpVerbsEnum::POST->value][] = $routeRepository;


        return $this->routes[HttpVerbsEnum::POST->value][array_key_last($this->routes[HttpVerbsEnum::GET->value])];
    }

    public function put(string $uri, array $action): RouteRepository
    {
        $routeRepository = new RouteRepository()
            ->setUri(trim($uri, '/'))
            ->setHttpVerb(HttpVerbsEnum::PUT)
            ->setAction($action);

        $this->routes[HttpVerbsEnum::PUT->value][] = $routeRepository;


        return $this->routes[HttpVerbsEnum::PUT->value][array_key_last($this->routes[HttpVerbsEnum::GET->value])];
    }

    public function delete(string $uri, array $action): RouteRepository
    {
        $routeRepository = new RouteRepository()
            ->setUri(trim($uri, '/'))
            ->setHttpVerb(HttpVerbsEnum::GET)
            ->setAction($action);

        $this->routes[HttpVerbsEnum::DELETE->value][] = $routeRepository;


        return $this->routes[HttpVerbsEnum::DELETE->value][array_key_last($this->routes[HttpVerbsEnum::GET->value])];
    }

    public function patch(string $uri, array $action): RouteRepository
    {
        $routeRepository = new RouteRepository()
            ->setUri(trim($uri, '/'))
            ->setHttpVerb(HttpVerbsEnum::GET)
            ->setAction($action);

        $this->routes[HttpVerbsEnum::PATCH->value][] = $routeRepository;


        return $this->routes[HttpVerbsEnum::PATCH->value][array_key_last($this->routes[HttpVerbsEnum::GET->value])];
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


    /**
     * @throws RouteNotFoundException
     */
    public function dispatchRoute(): void
    {
        foreach ($this->routes[$this->httpVerb->value] as $route => $callback) {
            $pattern = preg_replace('/\{[a-zA-Z_]+}/', '([a-zA-Z0-9-_]+)', $route);


            $pattern = "#^$pattern$#";

            if (preg_match($pattern, $this->currentUri, $matches)) {
                array_shift($matches);

                $this->resolveAction($callback, $matches);

                return;
            }
        }

        throw new RouteNotFoundException('Route ' . $this->currentUri . ' not found');
    }


    public function resolveAction(array $callback, array $matches): void
    {
        [$controller, $method] = $callback;

        Helper::resolve($controller)->{$method}(...$matches);
    }


}