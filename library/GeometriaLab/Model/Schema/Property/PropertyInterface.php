<?php

namespace GeometriaLab\Model\Schema\Property;

use Zend\Filter\FilterChain as ZendFilterChain,
    Zend\Filter\FilterInterface as ZendFilterInterface;

interface PropertyInterface
{
    public function setName($name);
    public function getName();

    public function setDefaultValue($value);
    public function getDefaultValue();

    public function prepare($value);
    /**
     * @abstract
     * @param ZendFilterInterface[] $filters
     */
    public function setFilters(array $filters);
    /**
     * @abstract
     * @return ZendFilterChain
     */
    public function getFilterChain();
}