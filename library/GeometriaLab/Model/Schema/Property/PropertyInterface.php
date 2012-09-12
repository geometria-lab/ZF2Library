<?php

namespace GeometriaLab\Model\Schema\Property;

use Zend\Filter\FilterChain as ZendFilterChain,
    Zend\Filter\FilterInterface as ZendFilterInterface,
    Zend\Validator\ValidatorChain as ZendValidatorChain,
    Zend\Validator\ValidatorInterface as ZendValidatorInterface;

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

    /**
     * @abstract
     * @param ZendValidatorInterface[] $validators
     */
    public function setValidators(array $validators);
    /**
     * @abstract
     * @return ZendValidatorChain
     */
    public function getValidatorChain();
}