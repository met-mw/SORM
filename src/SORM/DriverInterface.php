<?php
namespace SORM;


interface DriverInterface
{

    /**
     * Connecting
     *
     * @param bool $isPersistent
     * @return $this
     */
    public function connect($isPersistent = false);

    /**
     * Fetch row
     *
     * @return array
     */
    public function fetchRow();

    /**
     * Fetch row as assoc array
     *
     * @return array<string, mixed>
     */
    public function fetchRowAssoc();

    /**
     * Fetch all rows
     *
     * @return array[]
     */
    public function fetchAll();

    /**
     * Fetch all rows as assoc array
     *
     * @return array<string, mixed>[]
     */
    public function fetchAllAssoc();

    /**
     * Get last inserted PK
     *
     * @return int
     */
    public function getLastInsertedPK();

    /**
     * Detect columns
     *
     * @param EntityInterface $oEntity
     * @return string[]
     */
    public function detectColumns(EntityInterface $oEntity);

    /**
     * Execute query
     *
     * @param string $query Query string
     * @param array $parameters Parameters
     * @return $this
     */
    public function query($query, $parameters = []);

} 