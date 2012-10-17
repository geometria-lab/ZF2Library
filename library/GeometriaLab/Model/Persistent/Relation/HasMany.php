<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\CollectionInterface,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany as HasManyProperty;

class HasMany extends AbstractRelation
{
    /**
     * @var CollectionInterface
     */
    protected $targetModels = false;

    /**
     * @param bool $refresh
     * @return CollectionInterface
     */
    public function getTargetModels($refresh = false)
    {
        if ($refresh || $this->targetModels === false) {
            $targetMapper = call_user_func(array($this->getProperty()->getTargetModelClass(), 'getMapper'));

            $originPropertyValue = $this->getOriginModel()->get($this->getProperty()->getOriginProperty());

            if ($originPropertyValue === null) {
                $collectionClass = $targetMapper->getCollectionClass();
                $this->targetModels = new $collectionClass;
            } else {
                $query = $targetMapper->createQuery();
                $query->where(array($this->getProperty()->getTargetProperty() => $originPropertyValue));

                $this->targetModels = $targetMapper->getAll($query);
            }
        }

        return $this->targetModels;
    }

    /**
     * @param CollectionInterface $collection
     * @return HasMany
     */
    public function setTargetModels(CollectionInterface $collection)
    {
        $this->targetModels = $collection;

        return $this;
    }

    /**
     * @return int
     */
    public function removeTargetRelations()
    {
        $onDelete = $this->getProperty()->getOnDelete();

        if ($onDelete === HasManyProperty::DELETE_NONE) {
            return 0;
        }

        $targetModels = $this->getTargetModels(true);

        if ($targetModels->isEmpty()) {
            return 0;
        }

        foreach($targetModels as $targetModel) {
            if ($onDelete === HasManyProperty::DELETE_CASCADE) {
                $targetModel->delete();
            } elseif ($onDelete === HasManyProperty::DELETE_SET_NULL) {
                $targetModel->set($this->getProperty()->getTargetProperty(), null);
                $targetModel->save();
            }
        }

        return count($targetModels);
    }
}