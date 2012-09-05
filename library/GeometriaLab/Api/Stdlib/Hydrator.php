<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 02.08.12
 * Time: 12:59
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Stdlib;

use Zend\Stdlib\Exception\BadMethodCallException as ZendBadMethodCallException;

use GeometriaLab\Stdlib\Hydrator\Schema,
    GeometriaLab\Api\Mvc\Controller\Action\Fields;

/**
 *
 */
abstract class Hydrator implements \Zend\Stdlib\Hydrator\HydratorInterface
{
    /**
     * @var Fields
     */
    protected $fields;
    /**
     * @var Schema
     */
    protected $schema;

    /**
     *
     */
    public function __construct()
    {
        $schema = $this->createSchema();
        $this->setSchema($schema);
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

    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @param  Fields $fields
     * @throws ZendBadMethodCallException
     * @return array
     */
    public function extract($object, Fields $fields = null)
    {
        $result = array();

        $allFields = true;
        if (count($fields) && !isset($fields['*'])) {
            $allFields = false;
        }

        foreach ($this->schema->getProperties() as $property) {
            // get initial value
            $source = $property->getSource();
            $propertyName = $property->getName();

            if (!$allFields && !isset($fields[$propertyName])) {
                continue;
            }
            if (is_subclass_of($source, get_class())) {
                /* @var Hydrator $hydrator */
                $hydrator = new $source();
                if (!method_exists($hydrator, 'extract')) {
                    throw new ZendBadMethodCallException("Invalid hydrator for property '$propertyName'");
                }

                $childFields = null;
                if (isset($fields[$propertyName]) && $fields[$propertyName] !== true) {
                    $childFields = $fields[$propertyName];
                }

                $result[$propertyName] = isset($object->$propertyName) ? $hydrator->extract($object->$propertyName, $childFields) : null;
            } elseif (is_callable($source)) {
                $result[$propertyName] = call_user_func($source, $object);
            } else {
                $result[$propertyName] = $object->$source;
            }

            // cast value
            if ($property->hasFilters()) {
                $result[$propertyName] = $property->getFilterChain()->filter($result[$propertyName]);
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
        throw new ZendBadMethodCallException("Not implemented");
    }
}