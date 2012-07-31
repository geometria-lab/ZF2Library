<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne as HasOneProperty;

class HasOne extends AbstractRelation
{
    /**
     * @var ModelInterface
     */
    protected $targetModel;

    /**
     * @return ModelInterface|null
     * @throws \RuntimeException
     */
    public function getTargetModel($refresh = false)
    {
        if ($refresh) {
            $this->targetModel = null;
        }

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
     * @param ModelInterface $foreignModel
     * @return HasOne
     */
    public function setTargetModel(ModelInterface $foreignModel)
    {
        $this->targetModel = $foreignModel;

        return $this;
    }
}