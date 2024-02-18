<?php

use app\core\ApiRoute;
use app\core\Router;

require_once '../vendor/autoload.php';

try {

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 1));
    $dotenv->load();

    $router = new Router;
    $route = new ApiRoute($router);
    routes($route);
    $router->initialize();
    
} catch (\Throwable $th) {
    dd("{$th->getMessage()} in line {$th->getLine()} from file {$th->getFile()}");
}
