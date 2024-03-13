<?php

namespace app\core;

use app\database\model\Model;
use app\traits\CoreValidation;

class Parameter
{

   private string $patternForIdentifyRouteWithParamater = "/({)+([a-zA-Z0-9]:?)+(})/";
   private array $parametersPattern = [
      "number" => "/(^[0-9]{1,})/",
      "string" => "/([a-zA-Z]{1,})/",
      "id" => "/(^[0-9]{1,})/",
   ];
   private ?Model $model = null;

   use CoreValidation;

   public function checkTypeParameter(string $parameter, string $value)
   {
      $this->doesTheParameterTypeExist($parameter, $this->parametersPattern);
      $this->isValidParameterValue($parameter, $value);
      return $value;
   }

   public function parameter(string $route, array $parametersValues, Route $routeType): array|false
   {
      preg_match_all($this->patternForIdentifyRouteWithParamater, $route, $matches); 

      $parameters = $matches[0];

      if ($parameters) {

         $validatedParameters = [];

         foreach ($parameters as $index => $parameter) {

            $parameter = trim($parameter, "{}");

            if (!str_contains($parameter, ":")) {
               $this->routeType = $routeType;
               $validatedParameters[] = $this->checkTypeParameter($parameter, $parametersValues[$index]);
            } else {
               $this->routeType = $routeType;
               $validatedParameters[] = $this->bindParam($parameter, $parametersValues[$index]);
            }
         }

         return $validatedParameters;
      }

      return false;
   }

   public function bindParam(string $parameter, string $value)
   {
      $splittedParameter = explode(":", $parameter);
      if (!class_exists($model = "app\\database\\model\\" . ucfirst($splittedParameter[0]))) return;
      $model = new $model;
      $foundData = $model->where($splittedParameter[1], $value);
      $this->doesTheRouteExist($foundData);
      return $foundData;
   }
}
