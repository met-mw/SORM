<?php
/**
 * Created by PhpStorm.
 * User: metr
 * Date: 04.10.15
 */

namespace SORM;


use Exception;
use SORM\Classes\SORMRegistry;
use SORM\Interfaces\InterfaceDriver;
use SORM\Traits\TraitSetting;

/**
 * Class Driver
 *
 * Базовый класс драйвера доступа к данным
 */
abstract class Driver implements InterfaceDriver {

    use TraitSetting;

    static public function setup(array $settings = []) {
        $driver = new static($settings);
        SORMRegistry::add($driver);
    }

    static public function factory($className, $primaryKey = null) {
        if (!class_exists($className)) {
            throw new Exception("Модель {$className} не существует");
        }

        return new $className(SORMRegistry::get(self::cls()), $primaryKey);
    }

    static public function cls() {
        return get_called_class();
    }

    public function __construct(array $settings = []) {
        $this->setSettings($settings);
        $this->config();
    }

    abstract protected function config();

}