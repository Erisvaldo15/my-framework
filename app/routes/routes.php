<?php

use app\core\Route;
use app\controllers\admin\HomeController;

function routes(Route $route)
{
    $route->prefix("admin")->group(function ($route) {
        $route->get("/users", HomeController::class, "users");
        $route->get("/user/{user:firstName}/{id}", HomeController::class, "show");
        // $route->post("/users", HomeController::class, "store");
    });

    $route->get("/", HomeController::class, "index");
};
