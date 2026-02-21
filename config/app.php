<?php

use System\Helper\Helper;
use System\Providers\RouteServiceProvider;

return [
    'providers' => [
        RouteServiceProvider::class,
    ],
    'view' => [
        'base_path' => Helper::basePath('resources/views'),
    ]
];