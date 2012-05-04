<?php

class GeometriaLab_Validate_IsIterable extends Zend_Validate_Abstract
{
    const NOT_ITERABLE = 'notIterable';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_ITERABLE => "'%value%' was not iterable",
    );

    /**
     * @var GeometriaLab_Validate_IsIterable
     */
    static $_validator;

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is array or iterable object
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        if ((is_object($value) && $value instanceof Traversable) || is_array($value)) {
            return true;
        }

        $this->_error(self::NOT_ITERABLE);

        return false;
    }

    /**
     * Static isValid version.
     * Returns true if and only if $value is array or iterable object
     *
     * @static
     * @param $value
     * @return bool
     */
    static public function staticIsValid($value)
    {
        if (self::$_validator === null) {
            self::$_validator = new self;
        }

        return self::$_validator->isValid($value);
    }
}
