<?php

namespace GeometriaLab\Model\Schema\Property;

use GeometriaLab\Validator\ValidatorChain;

use Zend\Filter\FilterChain as ZendFilterChain;

interface PropertyInterface
{
    /**
     * @param string $name
     * @return PropertyInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param mixed $value
     * @return mixed
     */
    public function setDefaultValue($value);

    /**
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * @param $required
     * @return PropertyInterface
     */
    public function setRequired($required);

    /**
     * @return bool
     */
    public function isRequired();

    /**
     * @param bool $allowEmpty
     * @return PropertyInterface
     */
    public function setAllowEmpty($allowEmpty);

    /**
     * @return bool
     */
    public function isAllowEmpty();

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
     * @param ValidatorChain $validatorChain
     */
    public function setValidatorChain(ValidatorChain $validatorChain);
    /**
     * @abstract
     * @return ValidatorChain
     */
    public function getValidatorChain();

    /**
     * @param mixed $value
     * @return mixed
     */
    public function filterAndValidate($value);
}