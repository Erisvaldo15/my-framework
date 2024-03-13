<?php

namespace app\core;

use app\traits\CoreValidation;
use app\traits\Request;

class Router
{
    private Route $route;
    private Parameter $parameter;

    use Request;
    use CoreValidation;

    public function __construct()
    {
        $this->parameter = new Parameter;
    }

    public function initialize()
    {
        $currentRoute = $this->removeSlashFromEndOfUri($this->extractUri());

        $foundRoute = $this->searchByRoute($this->route->routesWithRequestType, $currentRoute);

        if (!$foundRoute) return;

        [, $controller, $method] = array_values($foundRoute["foundRoute"]);

        $this->doesTheControllerExist($controller);
        $this->doesTheMethodExist($controller, $method);

        $instanceControllerClass = new $controller;

        if (!$foundRoute["parameters"]) {
            $instanceControllerClass->$method();
        } else {
            $instanceControllerClass->$method(...$foundRoute["parameters"]);
        }
    }

    public function addRoute(string $requestType, string $route, string $controller, string $method)
    {
        $requestType = strtoupper($requestType);

        $routeWithPrefix = "{$this->route->getPrefix()}{$route}";

        $routeWithPrefix = $this->removeSlashFromEndOfUri($routeWithPrefix);

        if (!isset($this->route->routesWithRequestType[$requestType])) {
            $this->route->routesWithRequestType[$requestType] = [];
        }

        $this->doesTheRouteAlreadyExist($routeWithPrefix, $this->route->routesWithRequestType);

        $this->route->routesWithRequestType[$requestType][] = [
            "route" => $routeWithPrefix,
            "controller" => $controller,
            "method" => $method,
        ];
    }

    private function searchByRoute(array $routesWithRequestType, string $currentRoute)
    {
        $parameters = null;
        $foundRoute = null;

        foreach ($this->route->routesWithRequestType as $requestType => $routesWithRequestType) {

            foreach ($routesWithRequestType as $routeData) {

                $route = $this->removeSlashFromEndOfUri($routeData['route']);
                $regex = preg_replace("/{([^}]+)}/", "([^/]+)", $route);
                $regex = "#^{$regex}$#";

                if (preg_match($regex, $currentRoute, $matches)) {
                    array_shift($matches);
                    $foundRoute = $routeData;
                    $parameters = $this->parameter->parameter($routeData['route'], $matches, $this->route);
                }
            };
        }

        $this->doesTheRouteExist($foundRoute);
        $this->isValidRequestType($requestType);

        return [
            "foundRoute" => $foundRoute,
            "parameters" => $parameters,
        ];
    }

    public function defineRouteInstance(Route $route): void
    {
        $this->route = $route;
        $this->routeType = $route;
    }

    private function removeSlashFromEndOfUri(string $routeWithPrefix): string
    {
        return substr($routeWithPrefix, -1) === "/" ? substr_replace($routeWithPrefix, "", strlen($routeWithPrefix) - 1) : $routeWithPrefix;
    }
}
