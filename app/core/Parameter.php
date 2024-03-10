<?php

namespace app\core;

use app\database\model\Model;

class Parameter
{

   private string $patternForIdentifyRouteWithParamater = "/({)+([a-zA-Z0-9]:?)+(})/";
   private array $parametersPattern = [
      "number" => "/(^[0-9]{1,})/",
      "string" => "/([a-zA-Z]{1,})/",
      "id" => "/(^[0-9]{1,})/",
   ];
   private ?Model $model = null;

   public function checkTypeParameter(string $parameter, string $value)
   {
      if (in_array($parameter, array_keys($this->parametersPattern))) {
         if (preg_match($this->parametersPattern[$parameter], $value)) {
            return $value;
            // se não for o tipo não for válido, retornar um 
         }
      }
   }

   public function parameter(string $route, array $parametersValues, Route $routeType): array|false
   {
      preg_match_all($this->patternForIdentifyRouteWithParamater, $route, $matches); // get all parameters definied in the route. 

      $parameters = $matches[0];

      if ($parameters) {

         $validatedParameters = [];

         foreach ($parameters as $index => $parameter) {

            $parameter = trim($parameter, "{}");

            if (!str_contains($parameter, ":")) {
               $validatedParameters[] = $this->checkTypeParameter($parameter, $parametersValues[$index]);
            } else {
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
      if ($foundData) return $foundData;
      http_response_code(404);
      die;
   }
}
