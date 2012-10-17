<?php

namespace GeometriaLab\Model\Persistent\Schema\Property\Relation;

use Zend\Validator\Callback as ZendCallback;

class BelongsTo extends AbstractRelation
{
    /**
     * @var string
     */
    protected $targetProperty = 'id';
    /**
     * @var string
     */
    protected $relationClass = '\GeometriaLab\Model\Persistent\Relation\BelongsTo';

    /**
     * Setup
     */
    public function setup() {
        $validator = new ZendCallback(array(
            'callback' => array($this, 'validate'),
            'message' => 'Must implement GeometriaLab\Model\Persistent\ModelInterface',
        ));
        $this->getValidatorChain()->addValidator($validator);
    }

    /**
     * Validate
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value)
    {
        return is_a($value, $this->getTargetModelClass());
    }
}