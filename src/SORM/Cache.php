<?php
namespace SORM;


class Cache
{

    static private $instance = null;

    private $columnsInfo = [];

    private function __construct() {}

    static public function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function push($tableName, array $columnsInfo)
    {
        $this->instance()->columnsInfo[$tableName] = $columnsInfo;
    }

    public function get($tableName)
    {
        return isset($this->columnsInfo[$tableName]) ? $this->columnsInfo[$tableName] : null;
    }

    public function clear()
    {
        $this->columnsInfo = [];
    }

} 