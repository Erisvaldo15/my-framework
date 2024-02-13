<?php

namespace app\core;

use Exception;

class Request
{

    private array $requestTypesAccepts = [
        "GET", "POST", "PUT", "DELETE"
    ];

    public function extractUri() {
        return (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    }

    public function extractCurrentRequestType() {
        return $_SERVER["REQUEST_METHOD"];
    }

    public function isRequestTypeExist(string $requestType): void
    {
        if (!in_array($requestType, $this->requestTypesAccepts)) throw new Exception("Request Type not accept.", 1);
    }

    public function isRequestTypeAllowed(string $currentRequestType)
    {
        if($_SERVER["REQUEST_METHOD"] !== $currentRequestType) {
            throw new Exception("{$currentRequestType} not allowed for this route", 1);
        }
    }
}
