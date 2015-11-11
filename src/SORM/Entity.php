<?php
namespace SORM;


use Exception;
use SORM\Interfaces\InterfaceDriver;
use SORM\Interfaces\InterfaceEntity;
use SORM\Tools\Builder\Delete;
use SORM\Tools\Builder\Insert;
use SORM\Tools\Builder\Select;
use SORM\Tools\Builder;
use SORM\Tools\Builder\Update;

abstract class Entity implements InterfaceEntity {

    const FIELD_TYPE_STRING = 's';
    const FIELD_TYPE_INTEGER = 'i';
    const FIELD_TYPE_DOUBLE = 'd';
    const FIELD_TYPE_BLOB = 'b';

    protected $isDeleted = false;

    protected $allowedFields = [];
    protected $fieldTypes = [];
    protected $fieldValues = [];

    protected $oneToOne = [];
    protected $oneToMany = [];
    protected $manyToOne = [];
    protected $manyToMany = [];

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

        if (!is_null($primaryKey)) {
            $this->load($primaryKey);
        }
    }

    public function __set($name, $value) {
        $this->fieldValues[$name] = $value;
    }

    public function __get($name) {
        return $this->fieldValues[$name];
    }

    public function builder() {
        return $this->builder;
    }

    public function load($primaryKey) {
        $driver = $this->driver;

        $query = $this->builder
            ->where([Builder::OPERAND_TYPE_F, $this->primaryKeyName], '=', [Builder::OPERAND_TYPE_V, $primaryKey])
            ->build();
        $driver->query($query);

        foreach ($driver->fetchAssoc() as $field => $value) {
            if (!in_array($field, $this->allowedFields)) {
                continue;
            }

            $this->fieldValues[$field] = $value;
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
        $driver = $this->driver;
        $select = new Select();

        $query = $select
            ->clearWhere()
            ->order(is_null($order) ? $this->primaryKeyName : $order, $direction)
            ->limit($limit)
            ->offset($offset)
            ->build();

        $driver->query($query);
        $entities = [];
        while ($result = $driver->fetchAssoc()) {
            $entity = new static($driver);
            foreach ($result as $field => $value) {
                $entity->{$field} = $value;
            }
            $entities[] = $entity;
        }

        return $entities;
    }

    public function getPrimaryKey() {
        return isset($this->fieldValues[$this->primaryKeyName])
            ? $this->fieldValues[$this->primaryKeyName]
            : null;
    }

    public function commit() {
        if ($this->isDeleted) {
            throw new Exception('Данные модели уже удалены.');
        }

        $driver = $this->driver;

        if (is_null($this->getPrimaryKey())) {
            $insert = new Insert();
            $insert
                ->table($this->tableName)
                ->fields(array_diff($this->allowedFields, [$this->primaryKeyName]));

            $values = [];
            foreach (array_diff($this->allowedFields, [$this->primaryKeyName]) as $field) {
                $values[] = $this->{$field};
            }
            $query = $insert
                ->values($values)
                ->build();

            $driver->query($query);
            $this->fieldValues[$this->primaryKeyName] = $driver->lastInsertId();
        } else {
            $update = new Update();
            $update
                ->table($this->tableName);

            foreach (array_diff($this->allowedFields, [$this->primaryKeyName]) as $field) {
                $update->set($field, '?');
            }
            $query = $update
                ->where([Builder::OPERAND_TYPE_F, $this->primaryKeyName], '=', [Builder::OPERAND_TYPE_P, '?'])
                ->build();

            $driver->prepare($query);

            $types = '';
            $attributes = [];
            foreach ($this->allowedFields as $field) {
                if ($field == $this->primaryKeyName) {
                    continue;
                }

                $types .= isset($this->fieldTypes[$field]) ? $this->fieldTypes[$field] : self::FIELD_TYPE_STRING;
                $attributes[] = $this->fieldValues[$field];
            }
            $types .= $this->fieldTypes[$this->primaryKeyName];
            $attributes[] = $this->getPrimaryKey();
            $driver->bindParameter($types, $attributes);
            $driver->execute();
        }

    }

    public function delete() {
        $delete = new Delete();

        $query = $delete
            ->table($this->tableName)
            ->where([Builder::OPERAND_TYPE_F, $this->primaryKeyName], '=', [Builder::OPERAND_TYPE_V, $this->getPrimaryKey()])
            ->build();

        $this->driver->query($query);
        foreach ($this->allowedFields as $field) {
            $this->fieldValues[$field] = null;
        }
        $this->isDeleted = true;
    }

    public function asJSON() {
        $jsonParts = [];
        foreach ($this->allowedFields as $field) {
            $jsonParts[] = "\"{$field}\": \"{$this->fieldValues[$field]}\"";
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

}