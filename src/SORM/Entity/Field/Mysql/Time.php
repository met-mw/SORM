<?php
namespace SORM\Entity\Field\Mysql;


use DateInterval;
use Exception;
use SORM\Entity\Field\Type;

class Time extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_STRING;

    /**
     * @param DateInterval $value
     *
     * @return string
     */
    public function toQueryWithQuotes($value) {
        return "'{$value->format('%H:%i:%s')}'";
    }

    public function toObject($value) {
        $intervalParts = explode(':', $value);
        $hours = ltrim($intervalParts[0], '0');
        $minutes = ltrim($intervalParts[1], '0');
        $seconds = ltrim($intervalParts[2], '0');

        $interval = DateInterval::createFromDateString("{$hours} hours + {$minutes} minutes + {$seconds} seconds");


        if (!$interval) {
            throw new Exception("Неизвестный формат времени: {$value}");
        }

        return $interval;
    }

    /**
     * @param DateInterval $value
     *
     * @return string
     */
    public function toQuery($value) {
        return $value->format('%H:%i:%s');
    }

}