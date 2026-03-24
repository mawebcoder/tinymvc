<?php

use System\Helper\Helper;
use System\Providers\RouteServiceProvider;
use System\Providers\CommandServiceProvider;

return [
    'providers' => [
        RouteServiceProvider::class,
        CommandServiceProvider::class
    ],

    'commands_path' => [
        Helper::basePath('Application/console')
    ]

];