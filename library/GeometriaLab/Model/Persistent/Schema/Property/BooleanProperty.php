<?php

namespace GeometriaLab\Model\Persistent\Schema\Property;

class BooleanProperty extends \GeometriaLab\Model\Schema\Property\BooleanProperty implements PropertyInterface
{
    /**
     * Primary property
     *
     * @var boolean
     */
    protected $isPrimary = false;

    /**
     * Persistent property
     *
     * @var boolean
     */
    protected $isPersistent = true;

    /**
     * Mark property as persistent (needs save to storage)
     *
     * @param boolean $persistent
     * @return PropertyInterface
     */
    public function setPersistent($persistent)
    {
        $this->isPersistent = $persistent;

        return $this;
    }

    /**
     * Is persistent (needs save to storage)
     *
     * @return boolean
     */
    public function isPersistent()
    {
        return $this->isPersistent;
    }

    /**
     * Mark property as primary
     *
     * @param boolean $primary
     * @return PropertyInterface
     */
    public function setPrimary($primary)
    {
        $this->isPrimary = $primary;

        return $this;
    }

    /**
     * Is primary
     *
     * @return boolean
     */
    public function isPrimary()
    {
        return $this->isPrimary;
    }
}