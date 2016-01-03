<?php
namespace SORM\Entity\Field\Mysql;


use SORM\Entity\Field\Type;

class Varchar extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_STRING;

    /**
     * @param string $value
     *
     * @return string
     */
    public function prepareToSQL($value) {
        return "'{$value}'";
    }

}