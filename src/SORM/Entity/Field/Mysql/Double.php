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
    public function toQueryWithQuotes($value) {
        return "{$this->toObject($value)}";
    }

    public function toObject($value) {
        return (double)$value;
    }

    public function toQuery($value) {
        return $this->toObject($value);
    }

}