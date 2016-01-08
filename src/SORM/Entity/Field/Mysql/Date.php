<?php
namespace SORM\Entity\Field\Mysql;


use DateTime;
use Exception;
use SORM\Entity\Field\Type;

class Date extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_STRING;

    private $format = 'Y-m-d';

    /**
     * @param DateTime $value
     *
     * @return string
     */
    public function toQueryWithQuotes($value) {
        return "'{$value->format($this->format)}'";
    }

    public function toObject($value) {
        $date = DateTime::createFromFormat($this->format, $value);
        $date->setTime(0, 0);

        if (!$date) {
            throw new Exception("Неизвестный формат даты: {$value}");
        }

        return  $date;
    }

    /**
     * @param DateTime $value
     *
     * @return mixed
     */
    public function toQuery($value) {
        return $value->format($this->format);
    }

}