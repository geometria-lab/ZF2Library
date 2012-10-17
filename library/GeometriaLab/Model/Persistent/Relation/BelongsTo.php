<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

class BelongsTo extends AbstractRelation
{
    /**
     * @var ModelInterface
     */
    protected $targetModel = false;

    /**
     * @param bool $refresh
     * @return ModelInterface|null
     * @throws \RuntimeException
     */
    public function getTargetModel($refresh = false)
    {
        if ($refresh || $this->targetModel === false) {
            $originPropertyValue = $this->getOriginModel()->get($this->getProperty()->getOriginProperty());

            if ($originPropertyValue !== null) {
                /* @var \GeometriaLab\Model\Persistent\Mapper\MapperInterface $targetMapper */
                $targetMapper = call_user_func(array($this->getProperty()->getTargetModelClass(), 'getMapper'));

                $condition = array($this->getProperty()->getTargetProperty() => $originPropertyValue);
                $query = $targetMapper->createQuery()->where($condition);

                $this->targetModel = $targetMapper->getOne($query);
            } else {
                $this->targetModel = null;
            }
        }

        return $this->targetModel;
    }

    /**
     * @param ModelInterface $targetModel
     * @return BelongsTo
     * @throws \InvalidArgumentException
     */
    public function setTargetModel(ModelInterface $targetModel = null)
    {
        if ($targetModel !== null) {
            $targetPropertyValue = $targetModel->get($this->getProperty()->getTargetProperty());

            if ($targetPropertyValue === null) {
                throw new \InvalidArgumentException('Target property is null');
            }

            $this->targetModel = false;
        } else {
            $targetPropertyValue = null;

            $this->targetModel = null;
        }

        $originPropertyName = $this->getProperty()->getOriginProperty();

        if ($this->getOriginModel()->get($originPropertyName) !== $targetPropertyValue) {
            $this->getOriginModel()->set($originPropertyName, $targetPropertyValue, true);
        }

        return $this;
    }

    public function resetTargetModel()
    {
        $this->targetModel = false;

        return $this;
    }
}