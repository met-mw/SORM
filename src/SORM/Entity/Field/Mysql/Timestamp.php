<?php
namespace SORM\Entity\Field\Mysql;


use Exception;
use SORM\Entity\Field\Type;

class Timestamp extends Type {

    protected $sqlParamType = Type::SQL_PARAM_TYPE_STRING;

    private $format = 'Y-m-d H:i:s';

    /**
     * @param \DateTime $value
     *
     * @return string
     */
    public function toQueryWithQuotes($value) {
        return "'{$value->format($this->format)}'";
    }

    public function toObject($value) {
        $date = \DateTime::createFromFormat($this->format, $value);

        if (!$date) {
            throw new Exception("Неизвестный формат даты: {$value}");
        }

        return  $date;
    }

    /**
     * @param \DateTime $value
     *
     * @return string
     */
    public function toQuery($value) {
        return $value->format($this->format);
    }

}