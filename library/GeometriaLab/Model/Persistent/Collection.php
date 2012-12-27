<?php

namespace GeometriaLab\Model\Persistent;

class Collection extends \GeometriaLab\Model\Collection implements CollectionInterface
{
    /**
     * Fetch models relations
     *
     * @param array|string|null $relationNames Array of relation names
     * @param bool $refresh
     * @return Collection
     */
    public function fetchRelations($relationNames = null, $refresh = false)
    {
        foreach ($this->parseRelationNames($relationNames) as $relationName => $childRelations) {
            $relation = $this->getRelation($relationName);
            if ($relation instanceof Relation\AbstractRelation) {
                $relation->setTargetObjectsToCollection($this, $refresh, $childRelations);
            }
        }

        $this->rewind();

        return $this;
    }

    /**
     * Parse relation names
     *
     * @param array|string|null $relationNames
     * @return array
     */
    protected function parseRelationNames($relationNames = null)
    {
        $relations = array();

        foreach ((array) $relationNames as $relationName => $childRelations) {
            if (is_numeric($relationName)) {
                if (!is_string($childRelations)) {
                    continue;
                }
                $relationName = $childRelations;
                $childRelations = null;
            }
            $relations[$relationName] = $childRelations;
        }

        return $relations;
    }

    /**
     * Get relation by name
     *
     * @param string $name Relation name
     * @return Relation\AbstractRelation|null
     */
    protected function getRelation($name)
    {
        $model = $this->offsetGet(0);
        if ($model instanceof AbstractModel && $model->hasRelation($name)) {
            return $model->getRelation($name);
        } else {
            return null;
        }
    }
}