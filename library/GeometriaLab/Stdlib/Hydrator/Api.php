<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 02.08.12
 * Time: 12:59
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Stdlib\Hydrator;

/**
 *
 */
abstract class Api implements \Zend\Stdlib\Hydrator\HydratorInterface
{
    /**
     * @var Schema
     */
    protected $schema;

    /**
     *
     */
    public function __construct()
    {
        $this->schema = $this->createSchema();
    }

    /**
     * @abstract
     * @return Schema
     */
    public abstract function createSchema();

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @throws \Zend\Stdlib\Exception\BadMethodCallException
     * @return array
     */
    public function extract($object)
    {
        $result = array();

        foreach ($this->schema->getProperties() as $property) {

            // get initial value
            $source = $property->getSource();
            if (is_callable($source)) {
                $result[$property->getName()] = call_user_func($source, $object);
            } else {
                $result[$property->getName()] = $object->$source;
            }

            // cast value
            if ($property->hasFilters()) {
                $result[$property->getName()] = $property->getFilterChain()->filter($result[$property->getName()]);
            }
        }

        return $result;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  object $object
     * @throws \Zend\Stdlib\Exception\BadMethodCallException
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        throw new \Zend\Stdlib\Exception\BadMethodCallException("Not implemented");
    }
}