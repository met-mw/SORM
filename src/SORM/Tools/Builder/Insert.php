<?php
namespace SORM\Tools\Builder;


use SORM\Tools\Builder;

class Insert extends Builder {

    protected $table;
    protected $fields = [];
    protected $valuesSet = [];

    public function table($table) {
        $this->table = $table;
    }

    public function fields(array $fields) {
        $this->fields = $fields;
        return $this;
    }

    public function field(array $field) {
        $this->fields[] = $field;
        return $this;
    }

    public function valuesSet(array $valuesSet) {
        $this->$valuesSet = $valuesSet;
        return $this;
    }

    public function values(array $values) {
        $this->valuesSet[] = $values;
        return $this;
    }

    public function build() {
        $fields = implode(', ', $this->fields);
        $valuesSet = '';
        foreach ($this->valuesSet as $values) {
            $valuesSet .= ' (';
            foreach ($values as $value) {
                $valuesSet .= is_numeric($value) ? $value : "'{$value}'";
            }
            $valuesSet .= ')';
        }
        $valuesSet = trim($valuesSet);
        $query = "insert into {$this->table} ({$fields}) {$valuesSet}";

        return $query;
    }

}