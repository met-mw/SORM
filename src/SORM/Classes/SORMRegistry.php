<?php
/**
 * Created by PhpStorm.
 * User: metr
 * Date: 21.10.15
 */

namespace SORM\Classes;


use SORM\Interfaces\InterfaceDriver;

class SORMRegistry {

    static private $container = [];

    /**
     * Получить данные из реестра
     *
     * @param string $name Имя записи
     *
     * @return InterfaceDriver
     */
    static public function get($name) {
        return self::$container[$name];
    }

    /**
     * Добавить данные в реестр
     *
     * @param InterfaceDriver $driver Драйвер
     */
    static public function add(InterfaceDriver $driver) {
        if (!isset(self::$container[$driver::cls()])) {
            self::$container[$driver::cls()] = $driver;
        }
    }

    private function __construct() {}

} 