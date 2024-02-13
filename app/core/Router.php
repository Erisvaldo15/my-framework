<?php

namespace app\core;

use Exception;

class Router
{

    private Request $request;
    private Controller $controller;
    private Method $method;
    private array $routes = [];

    public function __construct()
    {
        $this->request = new Request;
        $this->controller = new Controller($this->request);
        $this->method = new Method;
    }

    public function initialize()
    {
        $currentRequestType = $this->request->extractCurrentRequestType();
        $currentRoute = $this->request->extractUri();

        $findRoute = array_filter($this->routes[$currentRequestType], fn ($route) => $route["route"] === $currentRoute);

        if (!$findRoute) throw new Exception("Route does not find", 1);

        ["controller" => $controller, "method" => $method] = array_values($findRoute)[0];

        $this->controller->extractController($controller);
        $this->method->extractMethod($method, $controller);

        $instanceControllerClass = new $controller;
        $instanceControllerClass->$method();
    }

    public function addRoute(string $requestType, string $route, string $controller, string $method, string $prefix = "")
    {
        $requestType = strtoupper($requestType);

        $routeWithPrefix = "/{$prefix}{$route}";

        $routeWithPrefix = $this->formattedRouteWithPrefix($routeWithPrefix);

        if (!isset($this->routes[$requestType])) {
            $this->routes[$requestType] = [];
        }

        if (in_array($routeWithPrefix, $this->routes[$requestType])) {
            throw new Exception("The {$routeWithPrefix} Route already exists with the {$requestType} Request Type", 1);
        }

        $this->routes[$requestType][] = [
            "route" => $routeWithPrefix,
            "controller" => $controller,
            "method" => $method,
        ];
    }

    private function formattedRouteWithPrefix(string $routeWithPrefix): string {
        return substr($routeWithPrefix, -1) === "/" ? substr_replace($routeWithPrefix, "", strlen($routeWithPrefix) - 1) : $routeWithPrefix;
    }

}
