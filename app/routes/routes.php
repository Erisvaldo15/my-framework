<?php

use app\core\Route;
use app\controllers\admin\HomeController;

function routes(Route $route)
{
    $route->prefix("admin")->group(function ($route) {
        $route->get("/users", HomeController::class, "users");
        $route->get("/user/{user:firstName}/{number}", HomeController::class, "show");
    });

    $route->get("/", HomeController::class, "index");
};
