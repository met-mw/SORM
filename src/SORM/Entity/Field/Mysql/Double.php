<?php
namespace SORM\Entity\Field\Mysql;


use SORM\Entity\Field\Type;

class Double extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_DOUBLE;

    /**
     * @param double $value
     *
     * @return string
     */
    public function prepareToSQL($value) {
        return "{$this->prepareToObject($value)}";
    }

    public function prepareToObject($value) {
        return (double)$value;
    }

}