<?php

namespace GeometriaLab\Model\Persistent\Schema\Property;

interface PropertyInterface extends \GeometriaLab\Model\Schema\Property\PropertyInterface
{
    /**
     * @abstract
     * @param boolean $persistent
     * @return PropertyInterface
     */
    public function setPersistent($persistent);

    /**
     * @abstract
     * @return boolean
     */
    public function isPersistent();

    /**
     * @abstract
     * @param boolean $primary
     * @return PropertyInterface
     */
    public function setPrimary($primary);

    /**
     * @abstract
     * @return boolean
     */
    public function isPrimary();
}