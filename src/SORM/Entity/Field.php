<?php
namespace SORM\Entity;


use Exception;
use SORM\Entity\Field\Type;
use SORM\Entity;
use SORM\Interfaces\InterfaceEntity;

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

    /** @var InterfaceEntity */
    protected $entity;


    /**
     * @param \SORM\Interfaces\InterfaceEntity $entity
     * @param string $name Имя поля
     * @param Type $type Тип поля
     * @param bool $null Допускается null
     * @param int $key Первычный ключ
     * @param string|int|null $defaultValue Значение по умолчанию
     * @param string|int|null $value
     * @param string|null $extra
     */
    public function __construct(InterfaceEntity $entity, $name, Type $type, $null, $key, $defaultValue = null, $value = null, $extra = null) {
        $this->entity = $entity;
        $this->name = $name;
        $this->type = $type;
        $this->null = $null;
        $this->key = $key;
        $this->defaultValue = $defaultValue;
        $this->value = $value;
        $this->extra = $extra;
    }

    public function __get($modelName) {
        if (!isset($this->relations[$modelName])) {
            throw new Exception("Связь на модель \"{$modelName}\" не существует.");
        }

        return $this->relations[$modelName];
    }

    public function asSql() {
        return $this->type->toQuery($this->value);
    }

    public function asSqlWithQuotes() {
        return $this->type->toQueryWithQuotes($this->value);
    }

    public function getEntity() {
        return $this->entity;
    }

    public function isPrimary() {
        return $this->key == self::KEY_PRIMARY;
    }

} 