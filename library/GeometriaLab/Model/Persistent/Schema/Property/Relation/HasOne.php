<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use GeometriaLab\Model\Persistent\ModelInterface;

use Zend\Validator\Callback as ZendCallback;

class HasOne extends AbstractHasRelation
{
    protected $relationClass = '\GeometriaLab\Model\Persistent\Relation\HasOne';

    public function setup() {
        $validator = new ZendCallback(array(
            'callback' => array($this, 'validate'),
            'message' => 'Must implement GeometriaLab\Model\Persistent\ModelInterface',
        ));
        $this->getValidatorChain()->addValidator($validator);
    }

    public function validate($value)
    {
        return is_a($value, $this->getTargetModelClass());
    }
}