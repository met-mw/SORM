<?php
namespace SORM\Tools\Builder;


use SORM\Tools\SUD;

class Delete extends SUD {

    protected $tables = [];
    protected $usingTables = [];

    public function tables(array $tables) {
        $this->tables = $tables;
        return $this;
    }

    public function usingTables(array $usingTables) {
        $this->usingTables = $usingTables;
        return $this;
    }

    public function table($table) {
        $this->tables[] = $table;
        return $this;
    }

    public function usingTable($usingTable) {
        $this->usingTables[] = $usingTable;
        return $this;
    }


    public function build() {
        $tables = implode(', ', $this->tables);
        $usingTables = empty($this->usingTables) ? '' : ' using ' . implode(', ', $this->usingTables);
        $where = $this->buildWhere();
        if ($where != '') {
            $where = " {$where}";
        }
        $query = "delete from {$tables}{$usingTables}{$where}";

        return $query;
    }

}