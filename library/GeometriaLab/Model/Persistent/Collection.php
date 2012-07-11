<?php

namespace GeometriaLab\Model\Persistent;

use GeometriaLab\Model\Persistent\Schema\Property\Relation\AbstractRelation,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\BelongsTo,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne,
    GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany;

class Collection extends \GeometriaLab\Model\Collection implements CollectionInterface
{
    /**
     * Fetch models relations
     *
     * @param array $propertyNames
     * @return Collection|CollectionInterface
     * @throws \InvalidArgumentException
     */
    public function fetchRelations(array $propertyNames = array())
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $schema = $this->getFirst()->getSchema();

        // Get all model relations
        $relationProperties = array();
        foreach($schema->getProperties() as $name => $property) {
            if ($property instanceof AbstractRelation) {
                $relationProperties[$name] = $property;
            }
        }

        // Filter relations by argument
        if (!empty($propertyNames)) {
            $filteredRelationProperties = array();
            foreach($propertyNames as $name) {
                if (!isset($relationProperties[$name])) {
                    throw new \InvalidArgumentException("Property $name is not relation");
                }
                $filteredRelationProperties[$name] = $relationProperties[$name];
            }
            $relationProperties = $filteredRelationProperties;
        }

        // Fetch relations and set to models
        foreach($this->models as $model) {
            foreach($relationProperties as $name => $property) {
                $relation = null;
                if ($property instanceof BelongsTo) {


                    $relation = $property->getReferencedModel($model);
                } else if ($property instanceof HasOne) {


                    $relation = $property->getForeignModel($model);
                } else if ($property instanceof HasMany) {


                    $relation = $property->getForeignModels($model);
                }

                $model->set($name, $relation);
            }
        }

        return $this;
    }
}