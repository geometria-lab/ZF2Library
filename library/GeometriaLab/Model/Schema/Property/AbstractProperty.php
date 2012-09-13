<?php

namespace GeometriaLab\Model\Schema\Property;

use Zend\Filter\FilterInterface as ZendFilterInterface,
    Zend\Filter\FilterChain as ZendFilterChain,
    Zend\Filter\Exception\RuntimeException as ZendRuntimeException;

use Zend\Validator\Callback as ZendValidatorCallback,
    Zend\Validator\ValidatorInterface as ZendValidatorInterface,
    Zend\Validator\ValidatorChain as ZendValidatorChain;

abstract class AbstractProperty implements PropertyInterface
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
     * Required property
     *
     * @var boolean
     */
    protected $isRequired = false;

    /**
     * @var ZendFilterChain
     */
    protected $filterChain;

    /**
     * @var ZendValidatorChain
     */
    protected $validatorChain;

    /**
     * Type validators
     *
     * @var array
     */
    static protected $typeValidators = array();

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->filterChain = new ZendFilterChain();
        $this->validatorChain = new ZendValidatorChain();

        $this->setup();

        $this->setOptions($options);
    }

    /**
     * Set options
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            $method = "set$option";
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                throw new \InvalidArgumentException("Unknown property option '$option'");
            }
        }
    }

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

    /**
     * @param ZendFilterChain $filterChain
     * @return AbstractProperty
     */
    public function setFilterChain(ZendFilterChain $filterChain)
    {
        $this->filterChain = $filterChain;

        return $this;
    }

    /**
     * @param ZendFilterInterface[] $filters
     * @return AbstractProperty
     * @throws ZendRuntimeException
     */
    public function setFilters(array $filters)
    {
        foreach ($filters as $filter) {
            if (is_string($filter)) {
                $filter = array(
                    'name' => $filter,
                );
            }
            if (is_array($filter)) {
                if (!isset($filter['name'])) {
                    throw new ZendRuntimeException('Invalid filter specification provided; does not include "name" key');
                }
                $options = array();
                if (isset($filter['options'])) {
                    $options = $filter['options'];
                }
                $priority = ZendFilterChain::DEFAULT_PRIORITY;
                if (isset($filter['priority'])) {
                    $priority = intval($filter['priority']);
                }
                $this->getFilterChain()->attachByName($filter['name'], $options, $priority);
            } else {
                throw new ZendRuntimeException('Invalid filter declaration: need string or array');
            }
        }

        return $this;
    }

    /**
     * @return ZendFilterChain
     */
    public function getFilterChain()
    {
        return $this->filterChain;
    }

    /**
     * @param ZendValidatorChain $validatorChain
     * @return AbstractProperty
     */
    public function setValidatorChain(ZendValidatorChain $validatorChain)
    {
        $this->validatorChain = $validatorChain;

        return $this;
    }

    /**
     * @param ZendValidatorInterface[] $validators
     * @return AbstractProperty
     * @throws ZendRuntimeException
     */
    public function setValidators(array $validators)
    {
        foreach ($validators as $validator) {
            if (is_string($validator)) {
                $validator = array(
                    'name' => $validator,
                );
            }
            if (is_array($validator)) {
                if (!isset($validator['name'])) {
                    throw new ZendRuntimeException('Invalid validator specification provided; does not include "name" key');
                }
                $options = array();
                if (isset($validator['options'])) {
                    $options = $validator['options'];
                }
                $breakOnFailure = false;
                if (isset($validator['breakOnFailure'])) {
                    $breakOnFailure = intval($validator['breakOnFailure']);
                }
                $this->getValidatorChain()->addByName($validator['name'], $options, $breakOnFailure);
            } else {
                throw new ZendRuntimeException('Invalid validator declaration: need string or array');
            }
        }

        return $this;
    }

    /**
     * @return ZendValidatorChain
     */
    public function getValidatorChain()
    {
        return $this->validatorChain;
    }

    protected function setup()
    {

    }

    protected function addTypeValidator($type)
    {
        if (!isset(static::$typeValidators[$type])) {
            $validator = new ZendValidatorCallback();
            $validator->setOptions(array(
                'messageTemplates' => array(
                    ZendValidatorCallback::INVALID_VALUE => "Value must be a $type",
                ),
            ));
            $validator->setCallback(array(null, "is_$type"));

            static::$typeValidators[$type] = $validator;
        }

        $this->getValidatorChain()->addValidator(static::$typeValidators[$type]);
    }
}
