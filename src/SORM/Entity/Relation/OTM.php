<?php
namespace SORM\Entity\Relation;


use SORM\Entity\Relation;
use SORM\Interfaces\InterfaceEntity;

class OTM extends Relation {

    /** @var InterfaceEntity[] */
    protected $relations = [];

    public function prepare() {
        parent::prepare();

        $this->getModel()->builder()
            ->where("{$this->targetFieldName}={$this->currentField->value}");
    }

    /**
     * @return InterfaceEntity[]
     */
    public function load() {
        if ($this->loaded) {
            return $this->relations;
        }

        $this->prepare();
        /** @var InterfaceEntity[] $entities */
        $entities = $this->getModel()->findAll();

        // Устанавливаем обратную связь
        foreach ($entities as $entity) {
            $entity->field($this->targetFieldName)
                ->addRelationMTO($this->currentField->getEntity(), $this->currentField->name, true);
        }

        $this->loaded = true;

        return $entities;
    }

}