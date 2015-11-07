<?php
namespace SORM;


use Exception;
use SORM\Interfaces\InterfaceDriver;
use SORM\Interfaces\InterfaceEntity;

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

    public function __construct(InterfaceDriver $driver, $primaryKey = null) {
        $this->driver = $driver;

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

    public function load($primaryKey) {
        $driver = $this->driver;

        $query = "select * from {$this->tableName} where {$this->primaryKeyName}={$primaryKey}";
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

        $orderBy = 'order by ' .  (is_null($order) ? $this->primaryKeyName : $order) . " {$direction}";
        $query = "select * from {$this->tableName} {$orderBy}";
        if (!is_null($limit)) {
            $query .= "limit {$limit}";
        }
        if (!is_null($offset)) {
            $query .= "offset {$offset}";
        }

        $driver->query($query);
        $entities = [];
        foreach ($driver->fetchAssoc() as $row) {
            foreach ($row as $field => $value) {
                $entity = new static($driver);
                $entity->{$field} = $value;
                $entities[] = $entity;
            }
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
            $fields = implode(', ', $this->allowedFields);
            $values = implode(', ', $this->fieldValues);

            $query = "insert into {$this->tableName} ({$fields}) values ({$values})";
            $driver->query($query);
            $this->fieldValues[$this->primaryKeyName] = $driver->lastInsertId();
        } else {
            $fields = [];
            foreach ($this->allowedFields as $field) {
                if ($field == $this->primaryKeyName) {
                    continue;
                }

                $fields[] = "{$field}=?";
            }
            $fieldsQuery = implode(', ', $fields);

            $query = "update {$this->tableName} set {$fieldsQuery} where {$this->primaryKeyName}=?";
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
        $query = "delete from {$this->tableName} where {$this->primaryKeyName}={$this->getPrimaryKey()}";
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