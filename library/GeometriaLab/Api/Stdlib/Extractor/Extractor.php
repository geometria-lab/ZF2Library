<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 02.08.12
 * Time: 12:59
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Stdlib\Extractor;

use Zend\Stdlib\Hydrator\HydratorInterface as ZendHydratorInterface,
    Zend\Stdlib\Exception\BadMethodCallException as ZendBadMethodCallException;

use GeometriaLab\Api\Stdlib\Extractor\Schema,
    GeometriaLab\Api\Exception\WrongFields;

abstract class Extractor
{
    /**
     * @var Schema
     */
    protected $schema;

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
     * @param  array $fields
     * @return array
     * @throws WrongFields
     * @throws ZendBadMethodCallException
     */
    public function extract($object, $fields = array())
    {
        $result = array();

        $allFields = !count($fields);

        $schemaProperties = $this->getSchema()->getProperties();

        if (!$allFields) {
            foreach ($fields as $name => $value) {
                if (!isset($schemaProperties[$name])) {
                    throw new WrongFields('Wrong fields provided');
                }
                $selectProperties[$name] = $schemaProperties[$name];
            }
        }

        foreach ($schemaProperties as $property) {
            // get initial value
            $source = $property->getSource();
            $propertyName = $property->getName();

            if (!$allFields && !isset($fields[$propertyName])) {
                continue;
            }

            if (is_callable($source)) {
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
}