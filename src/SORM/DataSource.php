<?php
namespace SORM;


use Exception;
use SORM\Drivers\Mysql;
use SORM\Interfaces\InterfaceDriver;
use SORM\Traits\TraitSetting;

/**
 * Class DataSource
 *
 * Базовый класс доступа к данным
 */
class DataSource {

    const DRIVER_MYSQL = 'MySQL';
    const DRIVER_POSTGRESQL = 'PostgreSQL';

    /** @var InterfaceDriver[] */
    static protected $drivers = [];

    /** @var string|null */
    static protected $current = null;

    static public function setup($uniqueName, array $settings = []) {
        switch ($settings['driver']) {
            case self::DRIVER_MYSQL:
                self::$drivers[$uniqueName] = new Mysql($settings);
                break;
            case self::DRIVER_POSTGRESQL:
                // TODO: Реализовать драйвер
                throw new Exception("Драйвер \"{$settings['driver']}\" не найден.");
                break;
            default:
                throw new Exception("Драйвер \"{$settings['driver']}\" не найден.");
        }
    }

    static public function setCurrent($uniqueName) {
        if (!isset(self::$drivers[$uniqueName])) {
            throw new Exception("Драйвер с именем \"{$uniqueName}\" не установлен.");
        }

        self::$current = $uniqueName;
    }

    static public function getCurrent() {
        if (is_null(self::$current)) {
            throw new Exception("Драйвер не выбран.");
        }

        return self::$drivers[self::$current];
    }

    static public function factory($className, $primaryKey = null) {
        if (!class_exists($className)) {
            throw new Exception("Модель \"{$className}\" не существует.");
        }

        return new $className(self::getCurrent(), $primaryKey);
    }

}