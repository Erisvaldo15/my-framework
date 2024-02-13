<?php

namespace app\core;

use Exception;

class Controller
{
    public function extractController(string $controller): void
    {
        if (!class_exists($controller)) {
            throw new Exception("{$controller} does not exist", 1);
        }
    }
}
