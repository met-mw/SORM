<?php
namespace SORM\Entity\Field\Mysql;


use SORM\Entity\Field\Type;

class Longblob extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_BLOB;

    /**
     * @param string $value
     *
     * @return string
     */
    public function toQueryWithQuotes($value) {
        return "'{$value}'";
    }

}