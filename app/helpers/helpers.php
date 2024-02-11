<?php 

function getNameClass(object|string $class): string {
    $reflectionClass = new ReflectionClass($class);
    return $reflectionClass->getShortName();
}