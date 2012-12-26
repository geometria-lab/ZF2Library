<?php

namespace GeometriaLab\Model\Persistent;

interface CollectionInterface extends \GeometriaLab\Model\CollectionInterface
{
    /**
     * Fetch models relations
     *
     * @abstract
     * @param array|string|null $relationNames Array of relation names
     * @param bool $refresh Refresh relations from storage
     * @return CollectionInterface
     */
    public function fetchRelations($relationNames = null, $refresh = false);
}