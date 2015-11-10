<?php
/**
 * Created by PhpStorm.
 * User: metr
 * Date: 11.11.15
 */

namespace SORM\Tools;


abstract class SUD extends Builder {

    protected $where = [];

    public function where($operand1, $operator, $operand2) {
        $this->where[] = [$operand1, $operator, $operand2];
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
        $where = '';
        foreach ($this->where as $proposal) {
            if (is_array($proposal)) {
                $where .= " {$proposal[0]}{$proposal[1]}{$proposal[2]}";
            } else {
                $where .= " {$proposal}";
            }
        }

        return trim($where);
    }

}