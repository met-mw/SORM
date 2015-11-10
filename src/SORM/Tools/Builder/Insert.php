<?php
namespace SORM\Tools\Builder;


use SORM\Tools\Builder;

class Insert extends Builder {

    protected $table;
    protected $fields = [];
    protected $valuesSet = [];

    public function table($table) {
        $this->table = $table;
        return $this;
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
            $valuesSet .= ', (';
            $valuesArray = [];
            foreach ($values as $value) {
                if (is_numeric($value)) {
                    $valuesArray[] = $value;
                } elseif (is_null($value)) {
                    $valuesArray[] = 'null';
                } else {
                    $valuesArray[] = "'{$value}'";
                }
            }
            $valuesSet .= implode(', ', $valuesArray);
            $valuesSet .= ')';
        }
        $valuesSet = trim($valuesSet, ', ');
        $query = "insert into {$this->table} ({$fields}) values {$valuesSet}";

        return $query;
    }

}