<?php
namespace SORM\Tools;



abstract class SUD extends Builder {

    protected $where = [];

    public function where($expression) {
        $this->where[] = $expression;

        return $this;
    }

    public function whereAnd() {
        $this->where[] = 'and';
        return $this;
    }

    public function whereOr() {
        $this->where[] = 'or';
        return $this;
    }

    public function whereBracketOpen() {
        $this->where[] = '(';
        return $this;
    }

    public function whereBracketClose() {
        $this->where[] = ')';
        return $this;
    }

    protected function buildWhere() {
        $where = empty($this->where) ? '' : 'where';
        foreach ($this->where as $expression) {
            $where .= " {$expression}";
        }

        if ($where != '') {
            $where = " {$where}";
        }

        return $where;
    }

    public function clearWhere() {
        $this->where = [];
        return $this;
    }

}