<?php
/**
 * Created by PhpStorm.
 * User: metr
 * Date: 11.11.15
 */

namespace SORM\Tools;


abstract class SUD extends Builder {

    protected $where = [
        '(',
        ['a', '=', 'b'],
        'and',
        ['c', '=', 'd'],
        ')'
    ];

    public function where($operand1, $operator, $operand2) {
        $this->where[] = [$operand1, $operator, $operand2];
    }

    public function whereAnd() {
        $this->where[] = 'and';
    }

    public function whereOr() {
        $this->where[] = 'or';
    }

    public function whereBracketOpen() {
        $this->where[] = '(';
    }

    public function whereBracketClose() {
        $this->where[] = ')';
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