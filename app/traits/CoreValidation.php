<?php

namespace app\traits;

use app\core\Route;
use app\core\WebRoute;

use Exception;

trait CoreValidation
{
    public Route $routeType;
    private array $errors = [
        "api" => [
            "controller" => "An unexpected error ocurred.",
            "invalidParameter" => "Invalid Data.",
            "routeNotFound" => "Route not found",
            "routeAlreadyExist" => "An unexpected error ocurred.",
            "invalidRequest" => "Type of Request not allowed",
            "invalidParameterType" => "An unexpected error ocurred.",
            "method" => "An unexpected error ocurred.",
        ],
        "web" => [
            "controller" => "Controller does not exist.",
            "invalidParameter" => "Type of Parameter Value passed is invalid.",
            "routeNotFound" => "Route not found",
            "routeAlreadyExist" => "Route already exists",
            "invalidRequest" => "Type of Request not allowed",
            "invalidParameterType" => "The Type of Parameter defined in the routes file does not exist.",
            "method" => "Method does not exist.",
        ],
    ];

    use Request;
    use JsonResponse;

    public function isValidRequestType(string $requestType)
    {
        if ($this->extractCurrentRequestType() !== strtoupper($requestType)) $this->errorType("invalidRequest", 405);
    }

    public function doesTheRouteExist(?array $route)
    {
        if (!$route) $this->errorType("routeNotFound", 404);
    }

    public function doesTheControllerExist(string $controller)
    {
        if (!class_exists($controller)) $this->errorType("controller", 500);
    }

    public function doesTheMethodExist(string $controller, string $method)
    {
        if (!method_exists($controller, $method)) $this->errorType("method", 500);
    }

    public function isValidParameterValue(string $parameter, string $value)
    {
        if (!preg_match($this->parametersPattern[$parameter], $value)) $this->errorType("invalidParameter", 422);
    }

    public function doesTheParameterTypeExist(string $parameter, array $parametersPattern)
    {
        if (!in_array($parameter, array_keys($parametersPattern))) $this->errorType("invalidParameterType", 500);
    }

    public function doesTheRouteAlreadyExist(string $route, array $routes)
    {
        if (in_array($route, $routes)) $this->errorType(500, "routeAlreadyExist");
    }

    private function errorType(string $typeOfError, int $status)
    {
        if ($this->routeType instanceof WebRoute) throw new Exception($this->errors['web'][$typeOfError], 1);
        $this->error($status, $this->errors['api'][$typeOfError]);
    }
}
