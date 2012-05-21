<?php

namespace GeometriaLab\Model\Definition\Property;

use \Zend\Stdlib\Options as ZendOptions;

abstract class AbstractProperty extends ZendOptions implements PropertyInterface
{
    /**
     * Name
     *
     * @var string
     */
    protected $name;

    /**
     * Default value
     *
     * @var mixin
     */
    protected $defaultValue;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param $name
     * @return GeometriaLab_Model_Definition_Property_Abstract
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get default value
     *
     * @return mixin
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set default value
     *
     * @return GeometriaLab_Model_Definition_Property_Abstract
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;

        return $this;
    }
}