<?php

namespace GeometriaLab\Model\Persistent;

interface CollectionInterface extends \GeometriaLab\Model\CollectionInterface
{
    /**
     * Fetch models relations
     *
     * @abstract
     * @param array $propertyNames
     * @return CollectionInterface
     */
    public function fetchRelations(array $propertyNames = array());
}