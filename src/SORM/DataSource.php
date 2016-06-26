<?php
namespace SORM;


use InvalidArgumentException;


/**
 * Data source
 *
 * Class DataSource
 * @package SORM
 */
class DataSource implements DataSourceInterface
{

    /** @var $this|null */
    static protected $instance = null;

    protected function __construct() {}

    /** @var DriverInterface[] */
    protected $drivers = [];
    /** @var string */
    protected $currentDriverUID = null;

    /**
     * @return static Get data source object (singleton)
     */
    static public function i()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return DriverInterface Get current driver
     */
    static public function d()
    {
        return static::i()->getCurrentDriver();
    }

    /**
     * @return static
     */
    public function __invoke()
    {
        return static::i();
    }

    /**
     * Add driver
     *
     * @param string $driverUID
     * @param DriverInterface $driver
     * @return $this
     */
    public function addDriver($driverUID, DriverInterface $driver)
    {
        if ($this->hasDriver($driverUID)) {
            throw new InvalidArgumentException("Driver with UID \"{$driverUID}\" is already exists.");
        }

        $this->drivers[$driverUID] = $driver;
        if (sizeof($this->drivers) == 1) {
            $this->setCurrentDriver($driverUID);
        }

        return $this;
    }

    /**
     * Get entity
     *
     * @param string $className
     * @param int|null $pk
     * @return EntityInterface
     */
    public function factory($className, $pk = null)
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException('Class name must be a string.');
        }

        if (!is_integer($pk) && !is_null($pk)) {
            throw new InvalidArgumentException('Primary key must be a integer or null.');
        }

        if (!class_exists($className)) {
            throw new InvalidArgumentException("Class \"{$className}\" not found.");
        }

        return new $className($this->getCurrentDriver(), $pk);
    }

    /**
     * Get current driver
     *
     * @return DriverInterface
     */
    public function getCurrentDriver()
    {
        return is_null($this->getCurrentDriverUID()) ? null : $this->getDriverByUID($this->getCurrentDriverUID());
    }

    /**
     * Get current driver unique identifier
     *
     * @return string
     */
    public function getCurrentDriverUID()
    {
        return $this->currentDriverUID;
    }

    /**
     * Get driver by unique identifier
     *
     * @param string $driverUID
     * @return DriverInterface
     */
    public function getDriverByUID($driverUID)
    {
        if (!$this->hasDriver($driverUID)) {
            throw new InvalidArgumentException("Driver with UID \"{$driverUID}\" not found.");
        }

        return $this->drivers[$driverUID];
    }

    /**
     * Get drivers
     *
     * @return DriverInterface[] <string, InterfaceDriver>[]
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * Get drivers unique identifiers
     *
     * @return string[]
     */
    public function getDriversUIDs()
    {
        return array_keys($this->getDrivers());
    }

    /**
     * Check driver exists by unique identifier
     *
     * @param string $driverUID
     * @return bool
     */
    public function hasDriver($driverUID)
    {
        if (!is_string($driverUID)) {
            throw new InvalidArgumentException('Driver UID must be a string.');
        }

        return isset($this->drivers[$driverUID]);
    }

    /**
     * Set current driver
     *
     * @param string $driverUID
     * @return $this
     */
    public function setCurrentDriver($driverUID)
    {
        if (!$this->hasDriver($driverUID)) {
            throw new InvalidArgumentException("Driver with UID \"{$driverUID}\" not found.");
        }

        $this->currentDriverUID = $driverUID;
        return $this;
    }

}