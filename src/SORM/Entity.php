<?php
namespace SORM;


use Exception;
use SORM\Entity\Cache;
use SORM\Entity\Field;
use SORM\Interfaces\InterfaceDriver;
use SORM\Interfaces\InterfaceEntity;
use SORM\Tools\Builder\Delete;
use SORM\Tools\Builder\Insert;
use SORM\Tools\Builder\Select;
use SORM\Tools\Builder;
use SORM\Tools\Builder\Update;

abstract class Entity implements InterfaceEntity {

    protected $isDeleted = false;

    /** @var Field[] */
    protected $fields = [];

    /** @var string */
    protected $tableName;
    /** @var string */
    protected $primaryKeyName = 'id';

    /** @var InterfaceDriver  */
    private $driver;

    /** @var Select */
    private $builder;

    public function __construct(InterfaceDriver $driver, $primaryKey = null) {
        $this->driver = $driver;
        $this->builder = new Select();
        $this->builder->table($this->tableName);
        $this->loadColumns();

        if (!is_null($primaryKey)) {
            $this->prepareRelations();
            $this->load($primaryKey);
        }
    }

    public function __set($name, $value) {
        $this->field($name)->value = $value;
    }

    public function __get($name) {
        return $this->field($name)->value;
    }

    abstract public function prepareRelations();

    /**
     * Получить объект поля модели
     *
     * @param string|null $name Имя поля
     *
     * @return Field Объект поля
     * @throws Exception
     */
    public function field($name = null) {
        $fieldName = is_null($name) ? $this->getPrimaryKeyName() : $name;
        if (!isset($this->fields[$fieldName])) {
            throw new Exception("Поле \"{$fieldName}\" не существует в модели \"{$this->tableName}\".");
        }

        return $this->fields[$fieldName];
    }

    public function getFields() {
        return $this->fields;
    }

    /**
     * Получить список полей модели
     *
     * @return string[] Имена полей модели
     */
    public function getFieldsNames() {
        return array_keys($this->fields);
    }

    public function builder() {
        return $this->builder;
    }

    /**
     * @return static[]
     */
    public function findAll() {
        $query = $this->builder()->build();
        return $this->resultToEntities($query);
    }

    public function load($primaryKey) {
        $driver = $this->driver;

        $query = $this->builder()
            ->where("{$this->primaryKeyName}={$primaryKey}")
            ->build();

        $driver->query($query);

        foreach ($driver->fetchAssoc() as $field => $value) {
            $this->{$field} = $this->field($field)->type->toObject($value);
        }
    }

    /**
     * @param string|null $order
     * @param string $direction
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return static[]
     */
    public function fetchAll($order = null, $direction = 'asc', $limit = null, $offset = null) {
        $select = new Select();

        $query = $select
            ->order(is_null($order) ? $this->primaryKeyName : $order, $direction)
            ->limit($limit)
            ->offset($offset)
            ->build();

        return $this->resultToEntities($query);
    }

    public function getPrimaryKey() {
        return $this->{$this->primaryKeyName};
    }

    public function getPrimaryKeyName() {
        return $this->primaryKeyName;
    }

    public function commit() {
        if ($this->isDeleted) {
            throw new Exception('Данные модели уже удалены.');
        }

        $driver = $this->driver;
        $allowedFields = array_diff($this->getFieldsNames(), [$this->primaryKeyName]);

        if (is_null($this->getPrimaryKey())) {
            $insert = new Insert();
            $insert->table($this->tableName)->fields($allowedFields);

            $values = [];
            foreach ($allowedFields as $field) {
                $values[] = $this->{$field};
            }
            $query = $insert
                ->values($values)
                ->build();

            $driver->query($query);
            $this->{$this->primaryKeyName} = $driver->lastInsertId();
        } else {
            $update = new Update();
            $update->table($this->tableName);

            foreach ($allowedFields as $field) {
                $update->set($field, '?');
            }
            $query = $update
                ->where("{$this->primaryKeyName}=?")
                ->build();

            $driver->prepare($query);

            $types = '';
            $parameters = [];
            foreach ($allowedFields as $field) {
                $currentField = $this->field($field);

                $types .= $currentField->type->getSQLParamType();
                $parameters[] = $currentField->asSql();
            }
            $primaryKeyField = $this->field();
            $types .= $primaryKeyField->type->getSQLParamType();
            $parameters[] = $primaryKeyField->asSql();

            $driver->bindParameter($types, $parameters);
            $driver->execute();
        }

    }

    public function delete() {
        $delete = new Delete();

        $query = $delete
            ->table($this->tableName)
            ->where("{$this->primaryKeyName}={$this->getPrimaryKey()}")
            ->build();

        $this->driver->query($query);
        foreach ($this->getFieldsNames() as $field) {
            $this->{$field} = null;
        }
        $this->isDeleted = true;
    }

    public function asJSON() {
        $jsonParts = [];
        foreach ($this->getFieldsNames() as $field) {
            $jsonParts[] = "\"{$field}\": \"{$this->{$field}}\"";
        }
        $json = '{' . implode(', ', $jsonParts) . '}';

        return $json;
    }

    public function asXML() {
        // TODO: Implement asXML() method.
    }

    final static public function cls() {
        return get_called_class();
    }

    /**
     * @param $query
     *
     * @return static[]
     */
    protected function resultToEntities($query) {
        $this->driver->query($query);
        $entities = [];
        while ($result = $this->driver->fetchAssoc()) {
            /** @var InterfaceEntity $entity */
            $entity = new static($this->driver);
            foreach ($result as $field => $value) {
                $entity->{$field} = $entity->field($field)->type->toObject($value);
            }
            $entity->prepareRelations();
            $entities[] = $entity;
        }

        return $entities;
    }

    protected function loadColumns() {
        $cachedColumnsInfo = Cache::instance()->get($this->tableName);
        if (is_null($cachedColumnsInfo)) {
            $this->driver->query("show columns from {$this->tableName}");
            $columnsInfo = [];
            while ($result = $this->driver->fetchRow()) {
                list($fieldName, $type, $null, $key, $default, $extra) = $result;

                $typeClass = $this->driver->getColumnTypeClass($type);
                $columnsInfo[$fieldName] = [
                    'fieldName' => $fieldName,
                    'type' => new $typeClass(),
                    'null' => $this->driver->detectFieldNull($null),
                    'key' => $this->driver->detectFieldKey($key),
                    'default' => $default,
                    'extra' => $extra
                ];
            }

            Cache::instance()->push($this->tableName, $columnsInfo);
        } else {
            $columnsInfo = Cache::instance()->get($this->tableName);
        }

        foreach ($columnsInfo as $columnInfo) {
            $field = new Field(
                $this,
                $columnInfo['fieldName'],
                $columnInfo['type'],
                $columnInfo['null'],
                $columnInfo['key'],
                $columnInfo['default'],
                null,
                $columnInfo['extra']);
            if ($field->isPrimary()) {
                $this->primaryKeyName = $field->name;
            }

            $this->fields[$columnInfo['fieldName']] = $field;
        }
    }

}