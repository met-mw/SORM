<?php
namespace SORM\Entity\Field\Mysql;


use SORM\Entity\Field\Type;

class Bigint extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_INTEGER;

    /**
     * @param int $value
     *
     * @return string
     */
    public function toQueryWithQuotes($value) {
        return "{$this->toObject($value)}";
    }

    public function toObject($value) {
        return (int)$value;
    }

    public function toQuery($value) {
        return $this->toObject($value);
    }

}