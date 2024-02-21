<?php

namespace app\database\resources;

use app\classes\Validation;
use app\database\Connection;
use app\database\model\Model;
use app\database\resources\Paginate;
use app\database\resources\Relationship;
use app\traits\Formatter;

use PDO;
use PDOStatement;

class QueryBuilder {

    private string $entity;
    private ?Model $model = null;
    private PDO $connection;
    private array $filters = [];
    private array $binds = [];
    private array $executionOrder = [
        "update", "select", "where", "whereIn", "order", "limit", "offset"
    ];
    public ?Relationship $relationship = null;
    public string $table;

    public function __construct(Model $model)
    {
        $this->model = $this->model ?? $model;
        $this->relationship = $this->relationship ?? new Relationship($this, $this->model);
        $this->connection = Connection::connection();
        $this->entity = $this->getEntity(getNameClass($model));
    }

    public function getEntity(Model|string $table): string {
        $entity = "app\\database\\entity\\{$table}Entity";
        Validation::classExists($entity);
        return $entity;
    }

    public function get(): array|false {
        $query = $this->dumpQuery();
        $preparedQuery = $this->prepareQuery($query);

        if($this->execute($preparedQuery)) {
            $data = $preparedQuery->fetchAll(PDO::FETCH_CLASS, $this->entity);
            return !$this->relationship->getRelations() ? $data : $this->relationship->makeRelations($data);
        }

        return false;
    }

    public function getOnly() {
        $query = $this->dumpQuery();
        $preparedQuery = $this->prepareQuery($query);
        
        if($this->execute($preparedQuery)) {
            $data = $preparedQuery->fetchObject($this->entity);
            return !$this->relationship->getRelations() ? $data : $this->relationship->makeRelations($data); 
        }

        return false;
    }

    public function select(array|string $fields = '*'): QueryBuilder {
        $this->filters['select'] = "select {$fields} from {$this->model->getTable()}";
        return $this;
    }

    public function insert(array $data): bool {
        $fieldsForRegister = "(". implode(",", array_map(fn($field) => $field, array_keys($data))).")";
        $bindValues = "(". implode(",", array_map(fn($field) => ":{$field}", array_keys($data))).")";

        $query = "insert into {$this->model->getTable()} {$fieldsForRegister} values {$bindValues}";

        array_map(fn($field, $value) => $this->bind($field, $value), array_keys($data), $data);

        $preparedQuery = $this->prepareQuery($query);
        return $this->execute($preparedQuery);
    }

    public function update(array $data) {
        $query = "update {$this->model->getTable()} set ";

        $fieldsForUpdate = array_map(function ($field, $value) {
            $this->bind($field, $value);
            return "{$field} = :{$field}";
        }, array_keys($data), $data);

        $fieldsForUpdate = implode(",", $fieldsForUpdate);
        $query .= $fieldsForUpdate;

        $query .= $this->dumpQuery();
        $query = $this->prepareQuery($query);
        return $this->execute($query);
    }

    public function delete() {
        $query = "delete from {$this->model->getTable()}";
        $query .= $this->dumpQuery();
        $preparedQuery = $this->prepareQuery($query);
        return $this->execute($preparedQuery);
    }

    public function paginate(int $itemsPerPage = 10, int $currentPage = 1): Paginate {

        $paginate = new Paginate($this);

        $paginate->setItemsPerPage($itemsPerPage);
        $paginate->setCurrentPage($currentPage);

        $paginate->setData($this->get());

        $this->clearFilters();

        $paginate->totalOfRegisters();

        return $paginate;
    }

    public function where(string $field, string $value, $logic = "="): QueryBuilder
    {
        Validation::thereIsValueInArray($logic, Validation::getOperators(), "Logic Operator invalid");

        $formattedField = $this->bind($field, $value);

        $where = "{$field} {$logic} :{$formattedField}";

        if (isset($this->filters['where'])) {
            $this->filters['where'] = "{$this->filters['where']} and {$where}";
            return $this;
        }

        $this->filters['where'] = " where {$where}";
        return $this;
    }

    public function whereIn(string $field, array|string $values): QueryBuilder
    {
        if (is_array($values)) $formattedValues = implode(",", $values);
        else $formattedValues = rtrim($values, ",");

        $this->filters['whereIn'] = "where {$field} in ({$formattedValues})";
        return $this;
    }

    public function orderBy(array $orders): QueryBuilder
    {
        $callback = function ($orderType, $sortByFilter) {
            $orderType = in_array($orderType, ["asc", "desc"]) ? $orderType : "asc";
            return "{$sortByFilter} {$orderType}";
        };

        $this->filters['order'] = "order by " . implode(",", array_map($callback, array_keys($orders), $orders));
        return $this;
    }

    public function limit(int $limit): QueryBuilder
    {
        $this->filters['limit'] = "limit {$limit}";
        return $this;
    }

    public function offset(int $offset): QueryBuilder
    {
        $this->filters['offset'] = "offset {$offset}";
        return $this;
    }

    private function clearFilters(): void {
        $this->filters = [];
        $this->binds = [];
    }

    private function bind(string $field, string $value): string {
        if(isset($this->binds[$field])) {
            $getAllEqualFields = array_filter(array_keys($this->binds), fn($fieldInArrayBind) => str_contains($fieldInArrayBind, $field));
            $field = "{$field}".count($getAllEqualFields) + 1;
        }

        $this->binds[$field] = $value;
        return $field;
    }

    private function prepareQuery(string $query): PDOStatement|false {
        return $this->binds ? $this->connection->prepare($query) : $this->connection->query($query);
    }

    private function dumpQuery(): string {
        $validscommands = array_filter($this->executionOrder, fn($command) => isset($this->filters[$command]));
        $query = implode(" ", array_map(fn($command) => $this->filters[$command], $validscommands));
        return $query;
    }

    private function execute(PDOStatement|false $query): bool {
        $result = $query->execute($this->binds);
        $this->clearFilters();
        return $result;
    }
}