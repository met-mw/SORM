<?php
namespace SORM\Interfaces;


interface InterfaceEntity {

    static public function cls();

    /**
     * @param int $primaryKey Первичный ключ
     */
    public function load($primaryKey);

    public function fetchAll($order = null, $direction = 'asc', $limit = null, $offset = null);

    public function getPrimaryKey();

    public function commit();

    public function delete();

    public function asJSON();

    public function asXML();

} 