<?php

namespace app\database\resources;

use app\database\entity\Entity;
use app\database\resources\QueryBuilder;
use app\database\model\Model;
use Exception;

class Relationship {

    private array $relations = [];
    private array $relationsType = [
        "belongsTo", "hasMany"
    ];
   
    public function __construct( private QueryBuilder $queryBuilder, private Model $model)
    {
        $this->queryBuilder = $queryBuilder;
        $this->model = $model;
    }

    public function addRelations(...$relations): QueryBuilder
    {
        $this->relations = $relations;
        return $this->queryBuilder;
    }

    public function getRelations(): array {
        return $this->relations;
    }

    private function relation(string $relationshipClass, string $typeRelationship, array|Entity $data, ?string $property = null): array|Entity
    {
        if (!in_array($typeRelationship, $this->relationsType)) throw new Exception("Relationship {$typeRelationship} unknown", 1);
        return $this->$typeRelationship($relationshipClass, $data, $property);
    }

    public function makeRelations(array|Entity $data): array|Entity
    {
        if(!$data) return $data; 
 
        $relationsCreated = [];

        foreach ($this->relations as $relation) {
            if (count($relation) < 2) throw new Exception("Few arguments were passed for parameter, were expected at least 2 arguments.", 1);
            [$relationshipClass, $typeRelationship, $property] = $relation + [2 => null]; // assign null for property value case be undefined.
            $relationsCreated[] = $this->relation($relationshipClass, $typeRelationship, $data, $property);
        }
        $this->clearRelations();
        return $relationsCreated[0];
    }

    private function belongsTo(string $relationshipClass, array|Entity $data, ?string $property = null): array|Entity
    {
        $property = strtolower($property ?? getNameClass($relationshipClass));
      
        $relationshipClass = new $relationshipClass;

        $foreignKey = strtolower(getNameClass($relationshipClass))."_id";

        if($data instanceof Entity) {

            $ids = $data->getAttributes()[$foreignKey];
            $dataFromRelationshipClass = $relationshipClass->whereIn("id", $ids);
    
            $data->$property = $dataFromRelationshipClass;

            return $data;
        }
   
        $ids = array_map(fn ($result) => $result->$foreignKey, $data);
     
        $dataFromRelationshipClass = $relationshipClass->whereIn("id", $ids);

        foreach ($data as $result) {

            foreach ($dataFromRelationshipClass as $dataFromRelationship) {

                if ($result->$foreignKey === $dataFromRelationship->id) {
                    $result->$property = $dataFromRelationship;
                }
            }
        }

        return $data;
    }

    private function hasMany(string $relationshipClass, array|Entity $data, ?string $property = null): array|Entity
    {
        $property = strtolower($property ?? getNameClass($relationshipClass));

        $relationshipClass = new $relationshipClass;

        $foreignKey = strtolower(getNameClass($this->model)."_id");

        if($data instanceof Entity) {
            $dataFromRelationshipClass = $relationshipClass->whereIn($foreignKey, $data->getAttributes()["id"]);
            $data->$property = $dataFromRelationshipClass;
            return $data;
        }

        $ids = array_map(fn ($result) => $result->id, $data);
        $dataFromRelationshipClass = $relationshipClass->whereIn($foreignKey, $ids);

        foreach ($data as $result) {

            $arrayComments = [];

            foreach ($dataFromRelationshipClass as $dataFromRelationship) {

                if ($result->id === $dataFromRelationship->$foreignKey) {
                    $arrayComments[] = $dataFromRelationship;
                }
            }

            $result->$property = $arrayComments;
        }

        return $data;
    }

    private function clearRelations(): void {
        $this->relations = [];
    }

}