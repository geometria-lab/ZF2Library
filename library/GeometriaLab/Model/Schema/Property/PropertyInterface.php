<?php

namespace GeometriaLab\Model\Schema\Property;

use Zend\Filter\FilterChain as ZendFilterChain,
    Zend\Validator\ValidatorChain as ZendValidatorChain;

interface PropertyInterface
{
    public function setName($name);
    public function getName();

    public function setDefaultValue($value);
    public function getDefaultValue();

    public function setRequired($required);
    public function isRequired();

    /**
     * @abstract
     * @param ZendFilterChain $filterChain
     */
    public function setFilterChain(ZendFilterChain $filterChain);
    /**
     * @abstract
     * @return ZendFilterChain
     */
    public function getFilterChain();

    /**
     * @abstract
     * @param ZendValidatorChain $validatorChain
     */
    public function setValidatorChain(ZendValidatorChain $validatorChain);
    /**
     * @abstract
     * @return ZendValidatorChain
     */
    public function getValidatorChain();
}