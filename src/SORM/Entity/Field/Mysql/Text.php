<?php
namespace SORM\Entity\Field\Mysql;


use SORM\Entity\Field\Type;

class Text extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_STRING;

    /**
     * @param string $value
     *
     * @return string
     */
    public function toQueryWithQuotes($value) {
        return "'{$value}'";
    }

}