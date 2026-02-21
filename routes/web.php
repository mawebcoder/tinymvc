<?php
use System\Router\Routing;
use Application\Controllers\AdminController;

Routing::get('/home', [AdminController::class, 'index']);