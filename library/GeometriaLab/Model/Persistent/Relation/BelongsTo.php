<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

class BelongsTo extends AbstractRelation
{
    /**
     * @var ModelInterface
     */
    protected $targetModel;

    /**
     * @return ModelInterface|null
     * @throws \RuntimeException
     */
    public function getTargetModel()
    {
        if ($this->targetModel === null) {
            $originPropertyValue = $this->getOriginModel()->get($this->getProperty()->getOriginProperty());

            if ($originPropertyValue === null) {
                return null;
            }

            /**
             * @var \GeometriaLab\Model\Persistent\Mapper\MapperInterface $targetMapper
             */
            $targetMapper = call_user_func(array($this->getProperty()->getTargetModelClass(), 'getMapper'));

            $condition = array($this->getProperty()->getTargetProperty() => $originPropertyValue);
            $query = $targetMapper->createQuery()->where($condition);

            $this->targetModel = $targetMapper->getOne($query);

            if ($this->targetModel === null) {
                throw new \RuntimeException('Invalid target model with: ' . json_encode($condition));
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
                throw new \InvalidArgumentException('Referenced property is null');
            }
        } else {
            $targetPropertyValue = null;
        }

        $originPropertyName = $this->getProperty()->getOriginProperty();

        if ($this->getForeignModel()->get($originPropertyName) !== $targetPropertyValue) {
            $this->getForeignModel()->set($originPropertyName, $targetPropertyValue);
        }

        return $this;
    }
}