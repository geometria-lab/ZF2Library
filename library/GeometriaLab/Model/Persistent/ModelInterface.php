<?php

namespace GeometriaLab\Model\Persistent;

interface ModelInterface extends \GeometriaLab\Model\ModelInterface
{
    /**
     * Save model to storage
     *
     * @return boolean
     */
    public function save();

    /**
     * Delete model from storage
     *
     * @return boolean
     */
    public function delete();

    /**
     * Is not saved model
     *
     * @return boolean
     */
    public function isNew();

    /**
     * Is model changed
     *
     * @return boolean
     */
    public function isChanged();

    /**
     * Is property changed
     *
     * @param string $name
     * @return boolean
     */
    public function isPropertyChanged($name);

    /**
     * Get changed property
     *
     * @return array
     */
    public function getChangedProperties();

    /**
     * Get property change
     *
     * @param string $name
     * @return array
     */
    public function getChange($name);

    /**
     * Get model changes
     *
     * @return array
     */
    public function getChanges();

    /**
     * Get clean property value
     *
     * @param string $name
     * @return mixed
     */
    public function getClean($name);

    /**
     * Mark model as clean
     *
     * @param boolean $flag
     * @return ModelInterface
     */
    public function markClean($flag = true);

    /**
     * Get mapper
     *
     * @static
     * @return Mapper\MapperInterface
     */
    static public function getMapper();

    /**
     * Create persistent model schema
     *
     * @static
     * @return Schema
     */
    static public function createSchema();
}
