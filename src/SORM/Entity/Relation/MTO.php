<?php
namespace SORM\Entity\Relation;


use SORM\DataSource;
use SORM\Entity\Relation;
use SORM\Interfaces\InterfaceEntity;

class MTO extends Relation {

    /** @var InterfaceEntity|null */
    protected $relation = null;

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
        $aEntities = $this->getModel()->findAll();
        $entity = reset($aEntities);

        // Устанавливаем обратную связь
        $entity->field($this->targetFieldName)
            ->addRelationOTM(DataSource::factory($this->currentField->getEntity()->cls()), $this->currentField->name);

        $this->loaded = true;

        return $entity;
    }

}