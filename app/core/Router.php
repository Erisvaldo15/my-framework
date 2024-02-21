<?php

namespace app\core;

use app\classes\Validation;
use app\traits\Request;

class Router
{
    private Route $route;

    use Request;

    public function initialize()
    {
        $currentRequestType = $this->extractCurrentRequestType();
        $currentRoute = $this->removeSlashFromEndOfUri($this->extractUri());

        $findRoute = array_filter($this->route->routes[$currentRequestType], fn ($route) => $route["route"] === $currentRoute);

        if (!$findRoute) {

            if ($this->route instanceof WebRoute) {
                throw new \Exception("Route not found", 1);
            }

            return http_response_code(404);
        }

        ["controller" => $controller, "method" => $method] = array_values($findRoute)[0];

        Validation::classExists($controller);
        Validation::methodExists($method, $controller);

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

        Validation::thereIsValueInArray($routeWithPrefix, $this->route->routes[$requestType]);

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
