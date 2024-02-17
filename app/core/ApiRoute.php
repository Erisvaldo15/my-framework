<?php

namespace app\core;

class ApiRoute extends Route
{

    public function __construct(private Router $router)
    {
        parent::__construct($router);
        $this->prefix = "/api"; 
    }
}
