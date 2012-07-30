<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\CollectionInterface;

class HasMany extends AbstractRelation
{
    /**
     * @var CollectionInterface
     */
    protected $targetModels;

    /**
     * @return CollectionInterface
     */
    public function getTargetModels()
    {
        if ($this->targetModels === null) {
            $targetMapper = call_user_func(array($this->getProperty()->getTargetModelClass(), 'getMapper'));

            $originPropertyValue = $this->getOriginModel()->get($this->getProperty()->getOriginProperty());

            if ($originPropertyValue === null) {
                $this->targetModels = $targetMapper->getCollectionClass();
            } else {
                $query = $targetMapper->createQuery();
                $query->where(array($this->getProperty()->getTargetProperty() => $originPropertyValue));

                $this->targetModels = $targetMapper->getAll($query);
            }
        }

        return $this->targetModels;
    }

    /**
     * @param CollectionInterface|null $collection
     * @return HasMany
     */
    public function setTargetModels(CollectionInterface $collection = null)
    {
        $this->targetModels = $collection;

        return $this;
    }
}