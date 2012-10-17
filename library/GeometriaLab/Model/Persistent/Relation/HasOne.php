<?php

namespace GeometriaLab\Model\Persistent\Relation;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne as HasOneProperty;

class HasOne extends AbstractRelation
{
    /**
     * @var ModelInterface
     */
    protected $targetModel = false;

    /**
     * @param bool $refresh
     * @return ModelInterface|null
     */
    public function getTargetModel($refresh = false)
    {
        if ($refresh || $this->targetModel === false) {
            $originPropertyValue = $this->getOriginModel()->get($this->getProperty()->getOriginProperty());

            if ($originPropertyValue !== null) {
                /**
                 * @var \GeometriaLab\Model\Persistent\Mapper\MapperInterface $targetMapper
                 */
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
     * @param ModelInterface $foreignModel
     * @return HasOne
     */
    public function setTargetModel(ModelInterface $foreignModel)
    {
        $this->targetModel = $foreignModel;

        return $this;
    }

    /**
     * @return int
     */
    public function removeTargetRelation()
    {
        $onDelete = $this->getProperty()->getOnDelete();

        if ($onDelete === HasOneProperty::DELETE_NONE) {
            return 0;
        }

        $targetModel = $this->getTargetModel(true);

        if ($targetModel === null) {
            return 0;
        }

        if ($onDelete === HasOneProperty::DELETE_CASCADE) {
            $targetModel->delete();
        } else if ($onDelete === HasOneProperty::DELETE_SET_NULL) {
            $targetModel->set($this->getProperty()->getTargetProperty(), null);
            $targetModel->save();
        }

        return 1;
    }
}