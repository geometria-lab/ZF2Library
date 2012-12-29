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
        if ($relationNames === null) {
            foreach ($this->getRelations() as $relation) {
                /* @var \GeometriaLab\Model\Persistent\Relation\AbstractRelation $relation */
                $relationNames[$relation->getProperty()->getName()] = null;
            }
        } else {
            $relationNames = $this->parseRelationNames($relationNames);
        }

        foreach ((array) $relationNames as $relationName => $childRelations) {
            $relation = $this->getRelation($relationName);
            if ($relation !== null) {
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
     * @throws \InvalidArgumentException
     */
    protected function parseRelationNames($relationNames = null)
    {
        $relations = array();

        foreach ((array) $relationNames as $relationName => $childRelations) {
            if (is_numeric($relationName)) {
                if (!is_string($childRelations)) {
                    throw new \InvalidArgumentException("Child relation must be a string but " . gettype($childRelations) . " is given.");
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
     * @throws \InvalidArgumentException
     */
    protected function getRelation($name)
    {
        $model = $this->getFirst();
        if ($model instanceof AbstractModel) {
            if (!$model->hasRelation($name)) {
                throw new \InvalidArgumentException("Model doesn't have '{$name}' relation");
            }
            return $model->getRelation($name);
        }
        return null;
    }

    /**
     * Get all relations
     *
     * @return Relation\AbstractRelation|null
     */
    protected function getRelations()
    {
        $model = $this->getFirst();
        if ($model instanceof AbstractModel) {
            return $model->getRelations();
        } else {
            return array();
        }
    }
}