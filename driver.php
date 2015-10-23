<?php
/**
 * Created by PhpStorm.
 * User: metr
 * Date: 04.10.15
 */

namespace Met_MW\SORM;


use Exception;
use Met_MW\SORM\Additional\Registry;
use Met_MW\SORM\Architecture\Interface_Driver;
use Met_MW\SORM\Extension\Trait_Setting;

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

        return new $className(Registry::get(self::cls()), $primaryKey);
    }

    static public function cls() {
        return get_called_class();
    }

}