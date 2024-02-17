<?php

namespace app\core;

use Exception;

class Router
{

    private Request $request;
    private Controller $controller;
    private Method $method;
    private Route $route;

    public function __construct()
    {
        $this->request = new Request;
        $this->controller = new Controller($this->request);
        $this->method = new Method;
    }

    public function initialize()
    {
        $currentRequestType = $this->request->extractCurrentRequestType();
        $currentRoute = $this->removeSlashFromEndOfUri($this->request->extractUri());

        $findRoute = array_filter($this->route->routes[$currentRequestType], fn ($route) => $route["route"] === $currentRoute);

        if (!$findRoute) throw new Exception("Route does not find", 1);

        ["controller" => $controller, "method" => $method] = array_values($findRoute)[0];

        $this->controller->extractController($controller);
        $this->method->extractMethod($method, $controller);

        $instanceControllerClass = new $controller;
        $instanceControllerClass->$method();
    }

    public function addRoute(string $requestType, string $route, string $controller, string $method)
    {
        $requestType = strtoupper($requestType);

        $routeWithPrefix = "{$this->route->getPrefix()}{$route}";

        $routeWithPrefix = $this->removeSlashFromEndOfUri($routeWithPrefix);

        if (!isset($this->route->routes[$requestType])) {
            $this->route->routes[$requestType] = [];
        }

        if (in_array($routeWithPrefix, $this->route->routes[$requestType])) {
            throw new Exception("The {$routeWithPrefix} Route already exists with the {$requestType} Request Type", 1);
        }

        $this->route->routes[$requestType][] = [
            "route" => $routeWithPrefix,
            "controller" => $controller,
            "method" => $method,
        ];
    }

    public function defineRouteInstance(Route $route): void
    {
        $this->route = $route;
    }

    private function removeSlashFromEndOfUri(string $routeWithPrefix): string
    {
        return substr($routeWithPrefix, -1) === "/" ? substr_replace($routeWithPrefix, "", strlen($routeWithPrefix) - 1) : $routeWithPrefix;
    }
}
