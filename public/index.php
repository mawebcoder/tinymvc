<?php


use Application\Controllers\AdminController;
use System\Exceptions\RouteNotFoundException;

ini_set('display_errors', 1);

spl_autoload_register(static function ($class) {
    require $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . str_replace(
            '\\',
            '/',
            $class
        ) . '.php';
});

$router = new \System\Router\Routing();

$router->get('/article/{id}', [AdminController::class, 'index']);


try {
    $router->dispatchRoute();
} catch (RouteNotFoundException $e) {

}





