<?php
namespace SORM\Tools\Builder;


use SORM\Tools\SUD;

class Delete extends SUD {

    protected $tables = [];
    protected $usingTables = [];

    public function tables(array $tables) {
        $this->tables = $tables;
    }

    public function usingTables(array $usingTables) {
        $this->usingTables = $usingTables;
    }

    public function table($table) {
        $this->tables[] = $table;
    }

    public function usingTable($usingTable) {
        $this->usingTables[] = $usingTable;
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