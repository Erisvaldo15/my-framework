<?php

namespace app\traits;

use Exception;

trait Request
{
    private array $requestTypesAccepts = [
        "GET", "POST", "PUT", "DELETE"
    ];
    private bool $allowCredentials = true;

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

    public function contentType(string $contentType): void {
        header("Content-type: {$contentType}");
    }

    public function requestTypesAccepts(): void {
        $allowedMethods = implode(",", $this->requestTypesAccepts);
        header("Access-Control-Allow-Methods: {$allowedMethods}");
    }

    public function allowedOrigin(): void {
        header("Access-Control-Allow-Origin: {$_ENV['API_ALLOWED_ORIGIN']}");
    }

    public function allowCredentials(): void {
        header("Access-Control-Allow-Credentials: {$this->allowCredentials}");
    }
}
