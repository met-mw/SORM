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
                $operand1 = is_numeric($proposal[0]) ? $proposal[0] : "'{$proposal[0]}'";
                $operand2 = is_numeric($proposal[2]) ? $proposal[2] : "'{$proposal[2]}'";
                $where .= " {$operand1}{$proposal[1]}{$operand2}";
            } else {
                $where .= " {$proposal}";
            }
        }

        return trim($where);
    }

}