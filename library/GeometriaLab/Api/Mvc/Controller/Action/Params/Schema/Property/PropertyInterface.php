<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property;

interface PropertyInterface extends \GeometriaLab\Model\Schema\Property\PropertyInterface
{
    /**
     * @abstract
     * @param boolean $primary
     * @return PropertyInterface
     */
    public function setRequired($primary);

    /**
     * @abstract
     * @return boolean
     */
    public function isRequired();
}