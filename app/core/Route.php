<?php

namespace app\core;

use Closure;

abstract class Route
{

    protected string $prefix = "";
    public array $routesWithRequestType = [];

    public function __construct(private Router $router)
    {
        $this->router->defineRouteInstance($this);
    }

    public function get(string $route, string $controller, string $method)
    {
        $this->router->addRoute('get', $route, $controller, $method);
    }

    public function post(string $route, string $controller, string $method)
    {
        $this->router->addRoute('post', $route, $controller, $method);
    }

    public function prefix(string $prefix): Route
    {
        $this->prefix = "{$this->prefix}/{$prefix}";
        return $this;
    }

    public function group(Closure $function)
    {
        $function($this);
        $this->prefix = "";
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
