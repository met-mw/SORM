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
    public function toQueryWithQuotes($value) {
        $bool = (int)$value;
        return "{$bool}";
    }

    public function toObject($value) {
        return (bool)$value;
    }

    public function toQuery($value) {
        return (int)$value;
    }

}