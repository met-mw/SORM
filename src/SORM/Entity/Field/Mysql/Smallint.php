<?php
namespace SORM\Entity\Field\Mysql;


use SORM\Entity\Field\Type;

class Smallint extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_INTEGER;

    /**
     * @param int $value
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