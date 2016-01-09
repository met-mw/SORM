<?php
namespace SORM\Entity;


use SORM\Interfaces\InterfaceEntity;

abstract class Relation {

    protected $loaded = false;
    /** @var Field */
    protected $currentField;
    /** @var string */
    protected $targetFieldName;

    /** @var InterfaceEntity */
    private $model;

    /**
     * @param Field $currentField
     * @param InterfaceEntity $model
     * @param string $targetFieldName
     * @param bool $loaded
     */
    public function __construct(Field $currentField, InterfaceEntity $model, $targetFieldName = null, $loaded = false) {
        $this->currentField = $currentField;
        $this->targetFieldName = is_null($targetFieldName) ? $model->getPrimaryKeyName() : $targetFieldName;
        $this->model = $model;
        $this->loaded = $loaded;
    }

    abstract public function load();

    public function prepare() {
        $this->getModel()->builder()->clearWhere();
    }

    /**
     * @return InterfaceEntity
     */
    public function getModel() {
        return $this->model;
    }

} 