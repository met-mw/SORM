<?php
namespace SORM;


use SORM\Interfaces\InterfaceDriver;
use SORM\Traits\TraitSetting;

abstract class Driver implements InterfaceDriver {

    use TraitSetting;

    public function __construct(array $settings = []) {
        $this->setSettings($settings);
        $this->config();
    }

    abstract protected function config();

}