<?php
namespace SORM\Entity;


use SORM\Entity\Field\Type;

class Field {

    const KEY_EMPTY = 0;
    const KEY_PRIMARY = 1;

    /** @var string Имя поля */
    public $name;
    /** @var Type Тип поля */
    public $type;
    /** @var bool Допускается null */
    public $null;
    /** @var int Ключ */
    public $key;
    /** @var int|null|string Значение по умолчанию */
    public $defaultValue;
    /** @var string */
    public $extra;

    public $value = null;

    /**
     * @param string $name Имя поля
     * @param Type $type Тип поля
     * @param bool $null Допускается null
     * @param int $key Первычный ключ
     * @param string|int|null $defaultValue Значение по умолчанию
     * @param string|int|null $value
     * @param string|null $extra
     */
    public function __construct($name, Type $type, $null, $key, $defaultValue = null, $value = null, $extra = null) {
        $this->name = $name;
        $this->type = $type;
        $this->null = $null;
        $this->key = $key;
        $this->defaultValue = $defaultValue;
        $this->value = $value;
        $this->extra = $extra;
    }

    public function asSql() {
        return $this->type->toQuery($this->value);
    }

    public function asSqlWithQuotes() {
        return $this->type->toQueryWithQuotes($this->value);
    }

    public function isPrimary() {
        return $this->key == self::KEY_PRIMARY;
    }

} 