<?php
/**
 * Created by PhpStorm.
 * User: metr
 * Date: 03.10.15
 */

namespace Met_MW\SORM\Architecture;


interface Interface_Entity {

    static public function cls();

    /**
     * @param int $primaryKey Первичный ключ
     */
    public function load($primaryKey);

    public function getPrimaryKey();

    public function commit();

    public function delete();

    public function asJSON();

    public function asXML();

} 