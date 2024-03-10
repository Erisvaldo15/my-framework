<?php

namespace app\core;

use app\classes\Validation;
use app\traits\Request;

class Router
{
    private Route $route;
    private Parameter $parameter;

    use Request;

    public function __construct()
    {
        $this->parameter = new Parameter;
    }

    public function initialize()
    {
        $currentRoute = $this->removeSlashFromEndOfUri($this->extractUri());

        $foundRoute = $this->searchByRoute($this->route->routesWithRequestType, $currentRoute);

        [,$controller, $method] = array_values($foundRoute["foundRoute"]);

        Validation::classExists($controller);
        Validation::methodExists($method, $controller);

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

        Validation::thereIsValueInArray($routeWithPrefix, $this->route->routesWithRequestType[$requestType]);

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

                /// status code 422 is the adequal.

                if (preg_match($regex, $currentRoute, $matches)) {

                    if (!$this->isValidRequestType($requestType)) {
                        http_response_code(405);
                        die;
                    }

                    array_shift($matches);
                    $foundRoute = $routeData;
                    $parameters = $this->parameter->parameter($routeData['route'], $matches, $this->route);
                }
            };
        }

        return [
            "foundRoute" => $foundRoute,
            "parameters" => $parameters,
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
