<?php

namespace app\database\model;

use app\database\entity\Entity;
use app\database\resources\QueryBuilder;
abstract class Model {

    protected string $table;
    private ?QueryBuilder $queryBuilder = null;

    public function __construct()
    {
        if(!$this->queryBuilder) {
            $this->queryBuilder = new QueryBuilder($this);
        }
    }

    public function all(array|string $fields = '*'): array|false {
        return $this->queryBuilder->select($fields)->get();
    }

    public function addRelations(...$relations) {
        $this->queryBuilder->relationship->addRelations(...$relations);
        return $this;
    }

    public function find(int $id, array|string $fields = "*"): Entity|null {
        return $this->queryBuilder->select($fields)->where("id", $id)->getOnly() ?? null;
    }

    public function create(array $data) {
        return $this->queryBuilder->insert($data);
    }

    public function update(array $newData) {
        return $this->queryBuilder->update($newData);
    }

    public function delete(): bool {
        return $this->queryBuilder->delete();
    }

    public function where(string $field, string $value, $logic = "="): QueryBuilder {
        return $this->queryBuilder->select()->where($field, $value, $logic);
    }

    public function whereIn(string $field, array|string $values): array|false {   
        return $this->queryBuilder->select()->whereIn($field, $values)->get();
    }

    public function getTable(): string {
        return $this->table;
    }
}