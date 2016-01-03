<?php
namespace SORM\Entity\Field;


use Exception;

abstract class Type {

    const SQL_PARAM_TYPE_STRING = 's';
    const SQL_PARAM_TYPE_INTEGER = 'i';
    const SQL_PARAM_TYPE_DOUBLE = 'd';
    const SQL_PARAM_TYPE_BLOB = 'b';

    protected $sqlParamType = null;

    abstract public function toQueryWithQuotes($value);

    public function toObject($value) {
        return $value;
    }

    public function toQuery($value) {
        return $value;
    }

    /**
     * @return string Возможные типы: s, i, d, b
     * @throws Exception
     */
    public function getSQLParamType() {
        if (is_null($this->sqlParamType)) {
            throw new Exception("Неизвестный тип параметра.");
        }

        return $this->sqlParamType;
    }

} 