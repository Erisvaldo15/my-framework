<?php

namespace app\core;

use Exception;

class Method {

    public function extractMethod(string $method, string|object $controller) {
        if(!method_exists($controller, $method)) {
            throw new Exception("{$method} method does not exist in {$controller}", 1);  
        }
    }

}