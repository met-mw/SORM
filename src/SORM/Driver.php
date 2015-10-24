<?php
/**
 * Created by PhpStorm.
 * User: metr
 * Date: 04.10.15
 */

namespace SORM;


use Exception;
use SORM\Classes\SORM_Registry;
use SORM\Interfaces\Interface_Driver;
use SORM\Traits\Trait_Setting;

/**
 * Class Driver
 *
 * Базовый класс драйвера доступа к данным
 */
abstract class Driver implements Interface_Driver {

    use Trait_Setting;

    static public function factory($className, $primaryKey = null) {
        if (!class_exists($className)) {
            throw new Exception("Модель {$className} не существует");
        }

        return new $className(SORM_Registry::get(self::cls()), $primaryKey);
    }

    static public function cls() {
        return get_called_class();
    }

}