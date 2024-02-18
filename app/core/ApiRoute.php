<?php

namespace app\core;

use app\traits\Request;

class ApiRoute extends Route
{
    private string $apiRequest = "application/json";
  
    use Request;

    public function __construct(private Router $router)
    {
        parent::__construct($router);
        $this->settings();
        $this->prefix = "/api"; 
    }

    private function settings() {
      $this->contentType($this->apiRequest);
      $this->allowCredentials();
      $this->allowedOrigin();
      $this->requestTypesAccepts();
    }
}
