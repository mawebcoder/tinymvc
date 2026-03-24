<?php

namespace System\Providers;

use System\Router\Routing;
use System\Helper\Helper;

class RouteServiceProvider
{

    public function register(): void
    {
        if (Helper::isRunningConsole()){
            return;
        }

        $files = glob(Helper::basePath('routes/*.php'));

        foreach ($files as $file) {
            Routing::loadFile($file);
        }
    }
}