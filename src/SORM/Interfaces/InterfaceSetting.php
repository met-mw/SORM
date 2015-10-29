<?php
namespace SORM\Interfaces;


interface InterfaceSetting {

    /**
     * @param $name
     * @param $value
     *
     * @return mixed
     */
    public function setSetting($name, $value);

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getSetting($name);

    /**
     * @return array
     */
    public function getSettings();

} 