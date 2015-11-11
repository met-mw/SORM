<?php
namespace SORM\Tools\Builder;


use SORM\Tools\SUD;

class Update extends SUD {

    protected $table;
    protected $sets = [];

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function sets(array $sets) {
        $this->sets = $sets;
        return $this;
    }

    public function set($field, $value) {
        $this->sets[] = [$field, $value];
        return $this;
    }

    public function build() {
        $setsArray = [];
        foreach ($this->sets as $set) {
            list($field, $value) = $set;
            $setsArray[] = "{$field}=" . (is_numeric($value) ? $value : "'{$value}'");
        }
        $sets = implode(', ', $setsArray);

        $where = $this->buildWhere();
        if ($where != '') {
            $where = " {$where}";
        }
        $query = "update {$this->table} set {$sets}{$where}";

        return $query;
    }

}