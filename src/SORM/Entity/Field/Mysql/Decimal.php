<?php
namespace SORM\Entity\Field\Mysql;


use SORM\Entity\Field\Type;

class Decimal extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_DOUBLE;

    /**
     * @param float $value
     *
     * @return string
     */
    public function toQueryWithQuotes($value) {
        return "{$this->toObject($value)}";
    }

    public function toObject($value) {
        return (float)$value;
    }

    public function toQuery($value) {
        return $this->toObject($value);
    }

}