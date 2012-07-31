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
        throw new \RuntimeException('Not implemented yet!');

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
        foreach($relationProperties as $property) {
            if ($property instanceof BelongsTo) {
                /**
                 * @var BelongsTo $value
                 */
                return $value->getReferencedModel();
            } else if ($property instanceof HasOne) {
                /**
                 * @var HasOne $value
                 */
                return $value->getForeignModel();
            } else if ($property instanceof HasMany) {
                /**
                 * @var HasMany $value
                 */
                return $value->getForeignModels();
            }
        }

        return $this;
    }
/*

    protected function setReferencedModelToCollection(CollectionInterface $collection, $refresh = false)
    {
        $foreignPropertyValues = array();
        foreach($collection as $model) {
            $foreignPropertyValue = $model->get($this->getForeignProperty());
            if ($foreignPropertyValue !== null && ($refresh || )) {
                $foreignPropertyValues[$this->getForeignProperty()][] = $model;
            }
        }




        // collect foregn keys values
        $localModels = array();
        foreach ($collection as $model) {
            // check that key is set in model
            // TODO 0 value will not pass check, should it ?
            if ($model->{$this->_localKey}) {
                if ($refresh || !$this->_issetModelForeignObject($model)) {
                    $localModels[$model->{$this->_localKey}][] = $model;
                    $model->getRelation($this->_relationName)->setForeignObjectNotFound();
                }
            }
        }

        // fetch foreign models
        $fetchParams = array($this->_foreignKey => array_keys($localModels));
        $foreignObjects = $this->_getForeignMapper()->fetchAll($fetchParams);
        if ($foreignObjects instanceof Geometria_Model_Collection_ExtendedInterface) {
            $foreignObjects->onFetchAsRelation($foreignObjectContext);
        }
        // set foreign objects in collection models
        foreach ($foreignObjects as $foreignObject) {
            foreach ($localModels[$foreignObject->{$this->_foreignKey}] as $localModel) {
                $localModel->{$this->_relationName} = $foreignObject;
            }
        }
    }
*/
}