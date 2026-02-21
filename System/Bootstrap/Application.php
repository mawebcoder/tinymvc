<?php

namespace System\Bootstrap;

use ReflectionException;
use System\Helper\Helper;
use System\Router\Routing;
use System\Exceptions\ConfigFileNotExistsException;

class Application
{


    /**
     * @throws ReflectionException
     * @throws ConfigFileNotExistsException
     */
    public function handle(): void
    {
        $this->runServiceProviders();

        $this->dispatchRoutes();
    }


    /**
     * @throws ReflectionException
     * @throws ConfigFileNotExistsException
     */
    private function runServiceProviders(): void
    {
        $providers = Helper::getConfig('app.providers');

        foreach ($providers as $provider) {
            if (!class_exists($provider)) {
                continue;
            }

            $provider = Helper::resolve($provider);

            $provider->register();
        }
    }

    /**
     * @throws ReflectionException
     */
    private function dispatchRoutes(): void
    {

        $router = Helper::resolve(Routing::class);

        $router->dispatchRoute();
    }

}