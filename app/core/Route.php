<?php

namespace app\core;

use Closure;

class Route {

    public array $routes = [];
    private string $prefix = "";

    public function __construct(private Router $router) {}

    public function get(string $route, string $controller, string $method) {
        $this->router->addRoute('get', $route, $controller, $method, $this->prefix);
    }

    public function post(string $route, string $controller, string $method) {
        $this->router->addRoute('post', $route, $controller, $method, $this->prefix);
    }

    public function prefix(string $prefix): Route {
        $this->prefix = $prefix;
        return $this;
    }

    public function group(Closure $function) {
        $function($this);
        $this->prefix = "";
    }
}