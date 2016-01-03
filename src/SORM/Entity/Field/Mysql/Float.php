<?php
namespace SORM\Entity\Field\Mysql;


use SORM\Entity\Field\Type;

class Float extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_DOUBLE;

    /**
     * @param float $value
     *
     * @return string
     */
    public function prepareToSQL($value) {
        return "{$this->prepareToObject($value)}";
    }

    public function prepareToObject($value) {
        return (float)$value;
    }

}