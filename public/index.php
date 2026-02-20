<?php


use Application\Controllers\AdminController;

ini_set('display_errors', 1);

spl_autoload_register(static function ($class) {
    require $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . str_replace(
            '\\',
            '/',
            $class
        ) . '.php';
});



var_dump(\System\Helper\Helper::resolve(\Application\Controllers\AdminController::class));





