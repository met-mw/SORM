<?php
namespace SORM\Entity\Field\Mysql;


use DateInterval;
use SORM\Entity\Field\Type;

class Year extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_INTEGER;

    /**
     * @param DateInterval $value
     *
     * @return string
     */
    public function prepareToSQL($value) {
        return "'{$value->y}'";
    }

    public function prepareToObject($value) {
        $interval = new DateInterval('Y');
        $interval->y = (int)$value;

        return $interval;
    }
}