<?php

use app\core\Route;
use app\core\Router;

require_once '../vendor/autoload.php';

try {
    $router = new Router;
    $route = new Route($router);
    routes($route);
    $router->initialize();
} catch (\Throwable $th) {
    dd("{$th->getMessage()} in line {$th->getLine()} from file {$th->getFile()}");
}