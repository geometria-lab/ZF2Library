<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\Relation;

use GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\PropertyInterface;

class BelongsTo extends \GeometriaLab\Model\Persistent\Schema\Property\Relation\BelongsTo implements PropertyInterface
{
    /**
     * @param bool $required
     * @return BelongsTo
     */
    public function setRequired($required)
    {
        $this->isRequired = $required;
        return $this;
    }
}