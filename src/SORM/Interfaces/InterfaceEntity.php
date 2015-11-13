<?php
namespace SORM\Interfaces;


use SORM\Tools\Builder\Select;

interface InterfaceEntity {

    static public function cls();

    /**
     * @return Select
     */
    public function builder();

    /**
     * @param int $primaryKey Первичный ключ
     */
    public function load($primaryKey);

    public function findAll();

    public function fetchAll($order = null, $direction = 'asc', $limit = null, $offset = null);

    public function getPrimaryKey();

    public function commit();

    public function delete();

    public function asJSON();

    public function asXML();

} 