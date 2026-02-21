<?php

use System\Helper\Helper;
use System\Bootstrap\Application;

require __DIR__ . '/../vendor/autoload.php';

Helper::resolve(Application::class)
    ->handle();








