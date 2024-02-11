<?php

namespace app\database\entity;

use Exception;

abstract class Entity {

    protected array $attributes = [];

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? throw new Exception("Property {$name} does not exist", 1);
    }

    public function getAttributes(): array {
        return $this->attributes;
    }
}