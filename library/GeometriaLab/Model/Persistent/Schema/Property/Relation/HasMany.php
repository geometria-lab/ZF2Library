<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\CollectionInterface;

use Zend\Validator\Callback as ZendCallback;

class HasMany extends AbstractHasRelation
{
    protected $relationClass = '\GeometriaLab\Model\Persistent\Relation\HasMany';

    public function setup() {
        $validator = new ZendCallback(array(
            'callback' => array($this, 'validate'),
            'message' => 'Must implement GeometriaLab\Model\Persistent\ModelInterface',
        ));
        $this->getValidatorChain()->addValidator($validator);
    }

    public function validate($value)
    {
        if (!$value instanceof CollectionInterface) {
            return false;
        }

        $model = $value->getFirst();

        if ($model !== null && !is_a($model, $this->getTargetModelClass())) {
            return false;
        }

        return true;
    }
}