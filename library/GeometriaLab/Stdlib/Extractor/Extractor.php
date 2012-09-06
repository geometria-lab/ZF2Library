<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 02.08.12
 * Time: 12:59
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Stdlib\Extractor;

use Zend\Stdlib\Exception\BadMethodCallException as ZendBadMethodCallException;

use GeometriaLab\Stdlib\Extractor\Schema,
    GeometriaLab\Stdlib\Extractor\Fields,
    GeometriaLab\Api\Exception\WrongFields;

/**
 *
 */
abstract class Extractor
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
     * @return array
     * @throws WrongFields
     * @throws ZendBadMethodCallException
     */
    public function extract($object, Fields $fields = null)
    {
        $result = array();
        $selectProperties = $schemaProperties = $this->schema->getProperties();
        $allFields = ($fields !== null) ? $fields->hasFields() : true;

        if (!$allFields) {
            foreach ($fields as $name => $value) {
                if (!isset($schemaProperties[$name])) {
                    throw new WrongFields('Wrong fields provided');
                }
                $selectProperties[$name] = $schemaProperties[$name];
            }
        }

        foreach ($selectProperties as $property) {
            // get initial value
            $source = $property->getSource();
            $propertyName = $property->getName();

            if (!$allFields && !isset($fields[$propertyName])) {
                continue;
            }
            if (is_subclass_of($source, get_class())) {
                /* @var Extractor $extractor */
                $extractor = new $source();
                if (!method_exists($extractor, 'extract')) {
                    throw new ZendBadMethodCallException("Invalid extractor for property '$propertyName'");
                }

                $childFields = null;
                if (isset($fields[$propertyName]) && $fields[$propertyName] !== true) {
                    $childFields = $fields[$propertyName];
                }

                $result[$propertyName] = isset($object->$propertyName) ? $extractor->extract($object->$propertyName, $childFields) : null;
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
}