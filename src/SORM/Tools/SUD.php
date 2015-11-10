<?php
/**
 * Created by PhpStorm.
 * User: metr
 * Date: 11.11.15
 */

namespace SORM\Tools;


use Exception;

abstract class SUD extends Builder {

    const WHERE_OPERAND_TYPE_F = 'field';
    const WHERE_OPERAND_TYPE_V = 'value';

    protected $where = [];

    public function where(array $operand1, $operator, array $operand2) {
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
        $where = empty($this->where) ? '' : 'where';
        foreach ($this->where as $proposal) {
            if (is_array($proposal)) {
                $where .= " {$this->buildOperand($proposal[0])}{$proposal[1]}{$this->buildOperand($proposal[2])}";
            } else {
                $where .= " {$proposal}";
            }
        }

        return $where;
    }

    private function buildOperand(array $operand) {
        list($operandType, $content) = $operand;
        if ($operandType == self::WHERE_OPERAND_TYPE_F) {
            $result = $content;
        } elseif ($operandType == self::WHERE_OPERAND_TYPE_V) {
            $result = is_numeric($content) ? $content : "'{$content}'";
        } else {
            throw new Exception("Неизвестный тип операнда \"{$operandType}\".");
        }

        return $result;
    }

}