<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

abstract class AbstractHasRelation extends AbstractRelation
{
    public function removeForeignRelations()
    {
        $onDelete = $this->getOnDelete();

        if ($onDelete == static::DELETE_NONE) {
            return 0;
        }

        $foreignMapper = call_user_func(array($this->getModelClass(), 'getMapper'));

        $referencedPropertyValue = $referencedModel->get($this->getReferencedProperty());

        $query = $foreignMapper->createQuery();
        $query->where(array($this->getForeignProperty() => $referencedPropertyValue));

        $foreignModels = $foreignMapper->getAll($query);

        foreach($foreignModels as $foreignModel) {
            if ($onDelete === static::DELETE_CASCADE) {
                $foreignModel->delete();
            } else if ($onDelete === static::DELETE_SET_NULL) {
                $foreignModel->set($this->getForeignProperty(), null);
                $foreignModel->save();
            } else {
                throw new \RuntimeException("Invalid relation '{$this->getName()}' delete mode");
            }
        }

        return count($foreignModels);
    }
}