<?php
namespace SORM\Interfaces;


use SORM\Entity\Field;
use SORM\Tools\Builder\Select;

interface InterfaceEntity {

    /**
     * @return string
     */
    static public function cls();

    /**
     * @param $name
     *
     * @return Field
     */
    public function field($name);

    /**
     * @return Select
     */
    public function builder();

    /**
     * @param int $primaryKey Первичный ключ
     */
    public function load($primaryKey);

    /**
     * @return static[]
     */
    public function findAll();

    /**
     * @param string $order
     * @param string $direction
     * @param int $limit
     * @param int $offset
     *
     * @return static[]
     */
    public function fetchAll($order = null, $direction = 'asc', $limit = null, $offset = null);

    public function getPrimaryKey();

    public function getPrimaryKeyName();

    public function commit();

    public function delete();

    public function asJSON();

    public function asXML();

} 