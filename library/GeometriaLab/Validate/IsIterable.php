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
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is contained in the haystack option. If the strict
     * option is true, then the type of $value is also checked.
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
}
