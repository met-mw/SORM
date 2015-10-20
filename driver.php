<?php
/**
 * Created by PhpStorm.
 * User: metr
 * Date: 04.10.15
 */

namespace sorm;


use Exception;
use sorm\classes\Orm_Registry;
use sorm\interfaces\Interface_Driver;
use sorm\traits\Trait_Setting;

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

        return new $className(Orm_Registry::get(self::cls()), $primaryKey);
    }

    static public function cls() {
        return get_called_class();
    }

}