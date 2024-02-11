<?php

namespace app\traits;

use Exception;

trait Formatter {

    public array $mapping = ["=", "<", ">", "<=", ">=", "<>" , "!=", "<=>"];

    public function compareValues(string $searchBy, string $logic, string $value): bool {   
        switch ($logic) {
            case in_array($logic, ["<>", "!="]):  return $searchBy !== $value;
            case '<':   return $searchBy < $value;
            case '>':   return $searchBy > $value;
            case '<=':  return $searchBy <= $value;
            case '>=':  return $searchBy >= $value;
            case "=": return $searchBy === $value;
            case '<=>': return $searchBy <=> $value;
            default: throw new Exception("Logic Operator invalid", 1);  
        }
    }
 
}