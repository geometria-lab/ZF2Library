<?php

namespace GeometriaLab\Model\Schema\Property;

use Zend\Filter\FilterInterface as ZendFilterInterface,
    Zend\Filter\FilterChain as ZendFilterChain,
    Zend\Filter\Exception\RuntimeException as ZendRuntimeException;

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
     * Filters chain
     *
     * @var ZendFilterChain
     */
    protected $filterChain;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->filterChain = new ZendFilterChain();

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
     * @param ZendFilterInterface[] $filters
     * @return AbstractProperty
     * @throws ZendRuntimeException
     */
    public function setFilters(array $filters)
    {
        foreach ($filters as $filter) {
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
                $this->filterChain->attachByName($filter['name'], $options, $priority);
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
}