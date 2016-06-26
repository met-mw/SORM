<?php
namespace SORM;


interface DataSourceInterface
{

    /**
     * Add driver
     *
     * @param string $driverUID
     * @param DriverInterface $driver
     * @return $this
     */
    public function addDriver($driverUID, DriverInterface $driver);

    /**
     * Get entity
     *
     * @param string $className
     * @param int|null $pk
     * @return EntityInterface
     */
    public function factory($className, $pk = null);

    /**
     * Get current driver
     *
     * @return DriverInterface
     */
    public function getCurrentDriver();

    /**
     * Get current driver unique identifier
     *
     * @return string
     */
    public function getCurrentDriverUID();

    /**
     * Get driver by unique identifier
     *
     * @param string $driverUID
     * @return DriverInterface
     */
    public function getDriverByUID($driverUID);

    /**
     * Get drivers
     *
     * @return DriverInterface[] <string, InterfaceDriver>[]
     */
    public function getDrivers();

    /**
     * Get drivers unique identifiers
     *
     * @return string[]
     */
    public function getDriversUIDs();

    /**
     * Check driver exists by unique identifier
     *
     * @param string $driverUID
     * @return bool
     */
    public function hasDriver($driverUID);

    /**
     * Set current driver
     *
     * @param string $driverUID
     * @return $this
     */
    public function setCurrentDriver($driverUID);

}