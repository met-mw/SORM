<?php
namespace SORM;


use Exception;
use InvalidArgumentException;
use SimpleXMLElement;
use SQueryBuilder\Query\Select;
use SQueryBuilder\QueryBuilder;
use stdClass;

abstract class Entity implements EntityInterface {

    /** @var bool Удалена-ли сущность */
    protected $isDeleted = false;
    /** @var string Имя поля-флага удаления сущности */
    protected $marksDeletedFieldName = 'deleted';

    /** @var array */
    protected $fields = [];
    /** @var string[]  */
    protected $fieldsDisplayNames = [];

    /** @var string */
    protected $tableName;
    /** @var string */
    protected $pkName = 'id';

    /** @var DriverInterface  */
    private $driver;

    /** @var QueryBuilder */
    private $builder;
    /** @var Select */
    private $select;


    public function __construct(DriverInterface $driver, $primaryKey = null) {
        $this->driver = $driver;
        $this->builder = new QueryBuilder();
        $this->fields = $this->getDriver()->detectColumns($this);

        if (!is_null($primaryKey)) {
            $this->load($primaryKey);
        }
    }

    /**
     * Get this object class name
     *
     * @return string
     */
    final static public function cls() {
        return get_called_class();
    }

    public function __set($name, $value) {
        if (!$this->hasField($name)) {
            throw new InvalidArgumentException("Field with name \"{$name}\" not found in \"{$this->cls()}\" model.");
        }

        $this->fields[$name] = $value;
    }

    public function __get($name) {
        if (!$this->hasField($name)) {
            throw new InvalidArgumentException("Field with name \"{$name}\" not found in \"{$this->cls()}\" model.");
        }

        return $this->fields[$name];
    }

    /**
     * Delete this entity
     *
     * @return $this
     * @throws Exception
     */
    public function delete()
    {
        if (!is_null($this->marksDeletedFieldName)) {
            if (!$this->hasField($this->marksDeletedFieldName)) {
                throw new Exception("Field with name \"{$this->marksDeletedFieldName}\" not found.");
            }

            $this->{$this->marksDeletedFieldName} = true;
            $this->save();
        } else {
            $delete = $this->builder->delete();
            $query = $delete->tables($this->getTableName())
                ->where($this->getPKName(), '=', '?')
                ->build();

            $this->getDriver()->query($query, [$this->getPK()]);
            foreach ($this->getFieldsNames() as $fieldName) {
                $this->{$fieldName} = null;
            }
        }

        $this->isDeleted = true;

        return $this;
    }

    /**
     * Fill entity
     *
     * @param <string, mixed>[] $data
     * @return $this
     */
    private function fill(array $data = [])
    {
        foreach ($data as $name => $value) {
            $this->{$name} = $value;
        }

        return $this;
    }

    /**
     * Get entity data as assoc array
     *
     * @return array
     */
    public function getAssocArray()
    {
        return $this->fields;
    }

    /**
     * Get driver
     *
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Get fields names list
     *
     * @return string[]
     */
    public function getFieldsNames()
    {
        return array_keys($this->fields);
    }

    /**
     * Get displayed fields names
     *
     * @return string[]
     */
    public function getFieldsDisplayNames()
    {
        return $this->fieldsDisplayNames;
    }

    /**
     * Get entity data as JSON
     *
     * @return string
     */
    public function getJSON()
    {
        $object = new stdClass();
        foreach ($this->getFieldsNames() as $name) {
            $object->{$name} = $this->{$name};
        }

        return json_encode($object);
    }

    /**
     * Get PK value
     *
     * @return int
     */
    public function getPK()
    {
        return $this->{$this->pkName};
    }

    /**
     * Get PK field name
     *
     * @return string
     */
    public function getPKName()
    {
        return $this->pkName;
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Get query builder
     *
     * @return Select
     */
    public function getQueryBuilder()
    {
        if (is_null($this->select)) {
            $this->select = $this->builder->select();
            $this->select->table($this->getTableName());
        }

        return $this->select;
    }

    /**
     * Get entity data as XML
     *
     * @return string
     */
    public function getXML()
    {
        $xml = new SimpleXMLElement('<entity/>');
        foreach ($this->fields as $field => $value) {
            $xml->addChild($field, $value);
        }

        return $xml->asXML();
    }

    /**
     * Check field exists by name
     *
     * @param string $name
     * @return bool
     */
    public function hasField($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Field name must be a string.');
        }

        return array_key_exists($name, $this->fields);
    }

    /**
     * Load entity by PK
     *
     * @param int $primaryKey PK
     */
    public function load($primaryKey)
    {
        if (!is_integer($primaryKey)) {
            throw new InvalidArgumentException('Primary key value must be a integer.');
        }
        $query = $this->getQueryBuilder()
            ->where($this->getPKName(), '=', '?')
            ->build();

        $this->getDriver()->query($query, [$primaryKey]);
        $row = $this->getDriver()->fetchRowAssoc();
        if (!empty($row)) {
            $this->fill($row);
        }
    }

    /**
     * Load all entities satisfying conditions
     *
     * @return static[]
     */
    public function loadAll()
    {
        $aEntities = [];

        $this->getDriver()->query($this->getQueryBuilder()->build());
        while ($row = $this->getDriver()->fetchRowAssoc()) {
            $aEntities[] = (new static($this->getDriver()))->fill($row);
        }

        return $aEntities;
    }

    /**
     * Check entity "new status"
     *
     * @return bool
     */
    public function isNew()
    {
        return is_null($this->getPK());
    }

    /**
     * Save entity data into data source
     *
     * @return $this
     * @throws Exception
     */
    public function save() {
        if ($this->isDeleted) {
            throw new Exception('Entity deleted.');
        }

        $driver = $this->driver;
        $allowedFields = array_diff(array_keys($this->getAssocArray()), [$this->getPKName()]);

        if ($this->isNew()) {
            $insert = $this->builder->insert();
            $insert->table($this->tableName)->fields($allowedFields);

            $values = [];
            foreach ($allowedFields as $field) {
                $values[] = $this->{$field};
            }
            $query = $insert
                ->values($values)
                ->build();

            $driver->query($query);
            $this->{$this->getPKName()} = $driver->getLastInsertedPK();
        } else {
            $update = $this->builder->update();
            $update->table($this->tableName);

            foreach ($allowedFields as $field) {
                $update->set($field, '?');
            }
            $query = $update
                ->where($this->getPKName(), '=', '?')
                ->build();

            $driver->query($query, $allowedFields);
        }

    }

    /**
     * Set table name
     *
     * @param string $tableName
     * @return $this
     */
    public function setTableName($tableName)
    {
        if (!is_string($tableName)) {
            throw new InvalidArgumentException('Table name must be a string.');
        }

        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Set PK name
     *
     * @param string $pkName
     * @return $this
     */
    public function setPKName($pkName)
    {
        if (!is_string($pkName)) {
            throw new InvalidArgumentException('Primary key name must be a string.');
        }

        $this->pkName = $pkName;
        return $this;
    }

}