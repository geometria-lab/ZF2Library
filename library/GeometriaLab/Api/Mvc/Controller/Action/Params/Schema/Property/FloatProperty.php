<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property;

class FloatProperty extends \GeometriaLab\Model\Schema\Property\FloatProperty implements PropertyInterface
{
    /**
     * Required property
     *
     * @var boolean
     */
    protected $isRequired = false;

    /**
     * Mark property as Required
     *
     * @param boolean $required
     * @return PropertyInterface
     */
    public function setRequired($required)
    {
        $this->isRequired = $required;

        return $this;
    }

    /**
     * Is required
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->isRequired;
    }
}