<?php

namespace app\database\resources;

use app\database\resources\QueryBuilder;

class Paginate {

    private int $itemsPerPage;
    private int $currentPage;
    private int $total;
    private array $data;

    public function __construct(private QueryBuilder $queryBuilder){}

    public function setItemsPerPage(int $itemsPerPage): void {
        $this->itemsPerPage = $itemsPerPage;
        $this->queryBuilder->limit($itemsPerPage);
    }
    
    public function setCurrentPage(int $currentPage): void {
        $this->currentPage = ($currentPage - 1) * $this->itemsPerPage;
        $this->queryBuilder->offset($this->currentPage);
    }

    public function totalOfRegisters(): void {
        $this->total = $this->queryBuilder->select("count(*) as total")->getOnly()->total;
    }

    public function generatePagination() {
        return "Generateeee";
    }

    public function getData(): array {
        return $this->data;
    }

    public function setData(array $data): void {
        $this->data = $data;
    }
}