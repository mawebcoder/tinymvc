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

    public static array $routeFiles = [];

    public ?HttpVerbsEnum $httpVerb = null;


    public static array $routes = [];

    /**
     * @throws HttpVerbIsNotValidException
     */
    public function __construct()
    {
        $this->parseUrl();

        $this->loadRouteFiles();
    }

    public static function loadFile(string $path): void
    {
        static::$routeFiles[] = $path;
    }

    public static function getRoutes(): array
    {
        return self::$routes;
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


    public static function get(string $uri, array $action): RouteRepository
    {
        $routeRepository = new RouteRepository()
            ->setUri(trim($uri, '/'))
            ->setHttpVerb(HttpVerbsEnum::GET)
            ->setAction($action);

        self::$routes[HttpVerbsEnum::GET->value][] = $routeRepository;

        return self::$routes[HttpVerbsEnum::GET->value][array_key_last(self::$routes[HttpVerbsEnum::GET->value])];
    }

    public static function post(string $uri, array $action): RouteRepository
    {
        $routeRepository = new RouteRepository()
            ->setUri(trim($uri, '/'))
            ->setHttpVerb(HttpVerbsEnum::POST)
            ->setAction($action);

        self::$routes[HttpVerbsEnum::POST->value][] = $routeRepository;


        return self::$routes[HttpVerbsEnum::POST->value][array_key_last(self::$routes[HttpVerbsEnum::POST->value])];
    }

    public static function put(string $uri, array $action): RouteRepository
    {
        $routeRepository = new RouteRepository()
            ->setUri(trim($uri, '/'))
            ->setHttpVerb(HttpVerbsEnum::PUT)
            ->setAction($action);

        self::$routes[HttpVerbsEnum::PUT->value][] = $routeRepository;


        return self::$routes[HttpVerbsEnum::PUT->value][array_key_last(self::$routes[HttpVerbsEnum::PUT->value])];
    }

    public static function delete(string $uri, array $action): RouteRepository
    {
        $routeRepository = new RouteRepository()
            ->setUri(trim($uri, '/'))
            ->setHttpVerb(HttpVerbsEnum::GET)
            ->setAction($action);

        self::$routes[HttpVerbsEnum::DELETE->value][] = $routeRepository;


        return self::$routes[HttpVerbsEnum::DELETE->value][array_key_last(self::$routes[HttpVerbsEnum::DELETE->value])];
    }

    public static function patch(string $uri, array $action): RouteRepository
    {
        $routeRepository = new RouteRepository()
            ->setUri(trim($uri, '/'))
            ->setHttpVerb(HttpVerbsEnum::GET)
            ->setAction($action);

        self::$routes[HttpVerbsEnum::PATCH->value][] = $routeRepository;


        return self::$routes[HttpVerbsEnum::PATCH->value][array_key_last(self::$routes[HttpVerbsEnum::PATCH->value])];
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

        if (!isset(self::$routes[$this->httpVerb->value])){
            header("HTTP/1.0 404 Not Found");

            throw new RouteNotFoundException('Route ' . $this->currentUri . ' not found');
        }

        foreach (self::$routes[$this->httpVerb->value] as $routeRepository) {
            /**
             * @type RouteRepository $routeRepository
             */

            $pattern = preg_replace('/\{[a-zA-Z_]+}/', '([a-zA-Z0-9-_]+)', $routeRepository->getUri());


            $pattern = "#^$pattern$#";

            if (preg_match($pattern, $this->currentUri, $matches)) {
                array_shift($matches);

                $this->resolveAction($routeRepository, $matches);

                return;
            }
        }

        header("HTTP/1.0 404 Not Found");

        throw new RouteNotFoundException('Route ' . $this->currentUri . ' not found');
    }


    public function resolveAction(RouteRepository $routeRepository, array $matches): void
    {
        [$controller, $method] = $routeRepository->getAction();

        Helper::resolve($controller)->{$method}(...$matches);
    }

    private function loadRouteFiles(): void
    {
        foreach (static::$routeFiles as $file) {
            require_once $file;
        }
    }


}