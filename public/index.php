<?php


use System\Bootstrap\Application;
use System\Helper\Helper;

ini_set('display_errors', 1);

spl_autoload_register(static function ($class) {
    require $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . str_replace(
            '\\',
            '/',
            $class
        ) . '.php';
});

$application = Helper::resolve(Application::class);

$application->handle();









