<?php
namespace SORM\Entity\Relation;


use SORM\DataSource;
use SORM\Entity\Relation;
use SORM\Interfaces\InterfaceEntity;

class OTO extends Relation {

    /** @var InterfaceEntity|null */
    public $relation = null;

    /**
     * @return InterfaceEntity
     */
    public function load() {
        if ($this->loaded) {
            return $this->relation;
        }

        $this->getModel()->builder()
            ->where("{$this->targetFieldName}={$this->currentField->value}");
        /** @var InterfaceEntity $entity */
        $entity = reset($this->getModel()->findAll());

        // Устанавливаем обратную связь
        $entity->field($this->targetFieldName)
            ->addRelationOTO($this->currentField->getEntity(), $this->currentField->name, true);

        $this->loaded = true;

        return $entity;
    }

}