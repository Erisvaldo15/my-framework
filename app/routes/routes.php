<?php

use app\core\Route;
use app\controllers\admin\HomeController;

function routes(Route $route)
{
    $route->prefix("admin")->group(function ($route) {
        $route->get("/", HomeController::class, "index");
        $route->get("/users", HomeController::class, "users");
        $route->post("/users", HomeController::class, "store");
    });

};
