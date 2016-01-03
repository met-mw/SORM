<?php
namespace SORM\Entity\Field\Mysql;


use SORM\Entity\Field\Type;

class Tinyint extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_INTEGER;

    /**
     * @param int|bool $value
     *
     * @return string
     */
    public function prepareToSQL($value) {
        $bool = (int)$value;
        return "{$bool}";
    }

    public function prepareToObject($value) {
        return (bool)$value;
    }

}