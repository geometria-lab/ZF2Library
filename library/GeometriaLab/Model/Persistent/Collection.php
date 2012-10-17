<?php

namespace GeometriaLab\Model\Persistent;

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