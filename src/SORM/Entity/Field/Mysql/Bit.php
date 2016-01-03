<?php
namespace SORM\Entity\Field\Mysql;


use SORM\Entity\Field\Type;

class Bit extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_INTEGER;

    /**
     * @param int|bool $value
     *
     * @return string
     */
    public function prepareToSQL($value) {
        return "{$this->prepareToObject($value)}";
    }

    public function prepareToObject($value) {
        return (int)$value;
    }

}