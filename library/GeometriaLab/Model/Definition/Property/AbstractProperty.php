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
     * @var mixed
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
     * @return AbstractProperty
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get default value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set default value
     *
     * @param $value
     * @return AbstractProperty
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;

        return $this;
    }
}