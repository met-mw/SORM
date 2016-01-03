<?php
namespace SORM;


use Exception;
use SORM\Interfaces\InterfaceDriver;
use SORM\Traits\TraitSetting;

abstract class Driver implements InterfaceDriver {

    use TraitSetting;

    protected $typeTemplates = [];
    protected $typeClasses = [];

    public function __construct(array $settings = []) {
        $this->setSettings($settings);
        $this->config();
    }

    abstract protected function config();

    public function getColumnTypeClass($type) {
        foreach ($this->typeTemplates as $key => $template) {
            if (preg_match($template, $type) === 1) {
                return $this->typeClasses[$key];
            }
        }

        throw new Exception("Не удалось определить класс по типу \"{$type}\".");
    }

}