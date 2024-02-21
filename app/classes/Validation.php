<?php

namespace app\classes;

use Exception;

class Validation
{
    private static array $operators = ["=", "<", ">", "<=", ">=", "<>" , "!=", "<=>"];

    public static function classExists(string $class, ?string $message = null): void
    {
        if (!class_exists($class)) self::validationErrorMessage($message) ?? throw new Exception("{$class} does not exist", 1);
    }

    public static function methodExists(string $method, string $class, ?string $message = null): void
    {
        if (!method_exists($method, $class)) self::validationErrorMessage($message) ?? throw new Exception("The {$method} does not exist in the {$class} Class", 1);
    }

    public static function thereIsValueInArray(mixed $value, array $array, ?string $message = null): void
    {
        if (!in_array($value, $array)) self::validationErrorMessage($message) ?? throw new Exception("{$value} does not exist in the array", 1);
    }

    public static function alreadyIsValueInArray(mixed $value, array $array, ?string $message = null): void
    {
        if (in_array($value, $array)) self::validationErrorMessage($message) ?? throw new Exception("{$value} already exists in the array", 1);
    }

    public static function length(string $value1, string $value2, string $operator = "=", ?string $message = null) {
        $result = self::compareValues($value1, $operator, $value2);
        if(!$result) self::validationErrorMessage($message) ?? throw new Exception("The two values ​​don't meet the condition", 1);
    }

    public static function getOperators(): array {
        return self::$operators;
    } 

    private static function compareValues(string $searchBy, string $logic, string $value)
    {
        switch ($logic) {
            case in_array($logic, ["<>", "!="]):
                return $searchBy !== $value;
            case '<':
                return $searchBy < $value;
            case '>':
                return $searchBy > $value;
            case '<=':
                return $searchBy <= $value;
            case '>=':
                return $searchBy >= $value;
            case "=":
                return $searchBy === $value;
            case '<=>':
                return $searchBy <=> $value;
            default:
                throw new Exception("Logic Operator invalid", 1);
        }
    }

    private static function validationErrorMessage(?string $message)
    {
        if (!$message) return false;
        throw new Exception($message, 1);
    }
}
