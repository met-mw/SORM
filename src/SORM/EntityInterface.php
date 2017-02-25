<?php
namespace SORM;

use SQueryBuilder\Query\Select;

interface EntityInterface
{

    /**
     * Get this object class name
     *
     * @return string
     */
    static public function cls();

    /**
     * Delete this entity
     *
     * @return $this
     */
    public function delete();

    /**
     * Get entity data as assoc array
     *
     * @return array
     */
    public function getAssocArray();

    /**
     * Get driver
     *
     * @return DriverInterface
     */
    public function getDriver();

    /**
     * Get fields names list
     *
     * @return string[]
     */
    public function getFieldsNames();

    /**
     * Get displayed fields names
     *
     * @return string[]
     */
    public function getFieldsDisplayNames();

    /**
     * Get entity data as JSON
     *
     * @return string
     */
    public function getJSON();

    /**
     * Get PK value
     *
     * @return int
     */
    public function getPK();

    /**
     * Get PK field name
     *
     * @return string
     */
    public function getPKName();

    /**
     * Get table name
     *
     * @return string
     */
    public function getTableName();

    /**
     * Get query builder
     *
     * @return Select
     */
    public function getQueryBuilder();

    /**
     * Get entity data as XML
     *
     * @return string
     */
    public function getXML();

    /**
     * Check field exists by name
     *
     * @param string $name
     * @return bool
     */
    public function hasField($name);

    /**
     * Check entity "new status"
     *
     * @return bool
     */
    public function isNew();

    /**
     * Load entity by PK
     *
     * @param int $primaryKey PK
     */
    public function load($primaryKey);

    /**
     * Load all entities satisfying conditions
     *
     * @param array $parameters
     * @return static[]
     */
    public function loadAll(array $parameters = []);

    /**
     * Save entity data into data source
     *
     * @return $this
     */
    public function save();

    /**
     * Set table name
     *
     * @param string $tableName
     * @return $this
     */
    public function setTableName($tableName);

    /**
     * Set PK name
     *
     * @param string $pkName
     * @return $this
     */
    public function setPKName($pkName);

    /**
     * Get count of entities
     *
     * @return int
     */
    public function getCount();

} 