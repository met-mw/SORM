<?php
namespace SORM\Tools;


use Exception;
use SORM\Tools\Builder\Select;

abstract class SUD extends Builder {

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
                $where .= " {$this->buildOperand($proposal[0])} {$proposal[1]} {$this->buildOperand($proposal[2])}";
            } else {
                $where .= " {$proposal}";
            }
        }

        if ($where != '') {
            $where = " {$where}";
        }

        return $where;
    }

    private function buildOperand(array $operand) {
        list($operandType, $content) = $operand;
        switch ($operandType) {
            case self::OPERAND_TYPE_F:
            case self::OPERAND_TYPE_P:
                $result = $content;
                break;
            case self::OPERAND_TYPE_V:
                if (is_numeric($content)) {
                    $result = $content;
                } elseif (is_null($content)) {
                    $result = 'null';
                } else {
                    $result = "'{$content}'";
                }
                break;
            case self::OPERAND_TYPE_O:
                /** @var Select $content */
                $result = "({$content->build()})";
                break;
            default:
                throw new Exception("Неизвестный тип операнда \"{$operandType}\".");
        }

        return $result;
    }

    public function clearWhere() {
        $this->where = [];
        return $this;
    }

}