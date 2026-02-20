<?php

use System\Router\Routing;
use System\Exceptions\RouteNotFoundException;
use Application\Controllers\AdminController;
ini_set('display_errors', 1);

spl_autoload_register(static function ($class) {
    require $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . str_replace(
            '\\',
            '/',
            $class
        ) . '.php';
});


$router = new Routing();

$router->get('/article/{id}', [AdminController::class, 'index']);


$router->dispatchRoute();





