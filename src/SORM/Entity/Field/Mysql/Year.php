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
    public function toQueryWithQuotes($value) {
        return "'{$value->y}'";
    }

    public function toObject($value) {
        $interval = new DateInterval('Y');
        $interval->y = (int)$value;

        return $interval;
    }

    /**
     * @param DateInterval $value
     *
     * @return int
     */
    public function toQuery(DateInterval $value) {
        return $value->y;
    }
}