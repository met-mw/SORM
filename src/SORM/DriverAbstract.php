<?php
namespace SORM;


abstract class DriverAbstract implements DriverInterface {

    protected $typeTemplates = [];
    protected $typeClasses = [];

    /**
     * Detect columns
     *
     * @param EntityInterface $oEntity
     * @return string[]
     */
    public function detectColumns(EntityInterface $oEntity)
    {
        $cachedColumnsInfo = Cache::instance()->get($oEntity->getTableName());
        if (is_null($cachedColumnsInfo)) {
            $this->query("show columns from {$oEntity->getTableName()}");
            $fields = [];
            while ($result = $this->fetchRow()) {
                $fields[$result[0]] = null;
            }

            Cache::instance()->push($oEntity->getTableName(), $fields);
        } else {
            $fields = Cache::instance()->get($oEntity->getTableName());
        }

        return $fields;
    }


}