<?php

namespace GeometriaLab\Model\Persistent;

use GeometriaLab\Model\Persistent\Schema\Property\Relation\AbstractRelation,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\BelongsTo,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany;

class Collection extends \GeometriaLab\Model\Collection implements CollectionInterface
{
    /**
     * @todo Implement fetching of nested relations
     * @param array $propertyNames
     * @return CollectionInterface|void
     * @throws \RuntimeException
     */
    public function fetchRelations(array $propertyNames = array())
    {
        throw new \RuntimeException('Not implemented yet!');
    }
}