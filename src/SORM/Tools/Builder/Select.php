<?php
namespace SORM\Tools\Builder;


use SORM\Entity;
use SORM\Tools\Builder;
use SORM\Tools\SUD;

class Select extends SUD {

    /** @var Entity */
    protected $entity;

    protected $tables = [];
    protected $sqlCalcFoundRows = null;
    protected $fields = [];
    protected $joins = [];
    protected $orders = [];
    protected $limit = null;
    protected $offset = null;

    public function entity(Entity $entity) {
        $this->entity = $entity;
    }

    public function tables(array $tables) {
        $this->tables[] = $tables;
        return $this;
    }

    public function table($table) {
        $this->tables[] = $table;
        return $this;
    }

    public function sqlCalcFoundRows() {
        $this->sqlCalcFoundRows = 'SQL_CALC_FOUND_ROWS';
        return $this;
    }

    public function fields(array $fields) {
        $this->fields = $fields;
        return $this;
    }

    public function field($name, $alias = null) {
        $this->fields[] = is_null($alias) ? $name : [$name, $alias];
        return $this;
    }

    public function orders(array $orders) {
        $this->orders = $orders;
        return $this;
    }

    public function order($field, $direction = 'asc') {
        $this->orders[] = [$field, $direction];
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }

    public function build() {
        $tables = empty($this->tables) ? '' : ' from ' . implode(', ', $this->tables);
        $limit = is_null($this->limit) ? '' : " limit {$this->limit}";
        $offset = is_null($this->offset) ? '' : " offset {$this->offset}";
        $calcFoundRows = is_null($this->sqlCalcFoundRows) ? '' : " {$this->sqlCalcFoundRows}";
        $query = "select{$calcFoundRows} {$this->buildFields()}{$tables}{$this->buildWhere()}{$this->buildOrder()}{$limit}{$offset}";

        return $query;
    }

    private function buildFields() {
        $fieldsArray = [];
        foreach ($this->fields as $field) {
            if (is_array($field)) {
                $fieldsArray[] = "{$field[0]} as {$field[1]}";
            } else {
                $fieldsArray[] = $field;
            }
        }

        return empty($fieldsArray) ? '*' : implode(', ', $fieldsArray);
    }

    private function buildOrder() {
        $orderArray = [];
        foreach ($this->orders as $order) {
            $orderArray[] = "{$order[0]} {$order[1]}";
        }

        $orders = implode(', ', $orderArray);
        if ($orders != '') {
            $orders = " order by {$orders}";
        }

        return $orders;
    }

}