<?php
/**
 * Created by PhpStorm.
 * User: metr
 * Date: 04.10.15
 */

namespace Kernel;


use Exception;
use Kernel\Classes\SORM_Registry;
use Kernel\Interfaces\Interface_Driver;
use Kernel\Traits\Trait_Setting;

/**
 * Class Driver
 * @package kernel\orm
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