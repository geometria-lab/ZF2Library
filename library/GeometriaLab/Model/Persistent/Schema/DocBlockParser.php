<?php
namespace GeometriaLab\Model\Persistent\Schema;

use GeometriaLab\Model\Schema\DocBlockParser as ModelDocBlockParser,
    GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface;

use Zend\Code\Reflection\DocBlock\Tag\MethodTag as ZendMethodTag;

class DocBlockParser extends ModelDocBlockParser
{
    /**
     * Regular properties class map
     *
     * @var array
     */
    static protected $regularPropertiesClassMap = array(
        'string'  => 'GeometriaLab\Model\Persistent\Schema\Property\StringProperty',
        'array'   => 'GeometriaLab\Model\Persistent\Schema\Property\ArrayProperty',
        'boolean' => 'GeometriaLab\Model\Persistent\Schema\Property\BooleanProperty',
        'float'   => 'GeometriaLab\Model\Persistent\Schema\Property\FloatProperty',
        'integer' => 'GeometriaLab\Model\Persistent\Schema\Property\IntegerProperty',
    );

    /**
     * Relations class map
     *
     * @var array
     */
    static protected $relationsClassMap = array(
        'hasOne'    => 'GeometriaLab\Model\Persistent\Schema\Property\Relation\HasOne',
        'hasMany'   => 'GeometriaLab\Model\Persistent\Schema\Property\Relation\HasMany',
        'belongsTo' => 'GeometriaLab\Model\Persistent\Schema\Property\Relation\BelongsTo',
    );

    /**
     * Model property class name
     *
     * @var string
     */
    static protected $modelPropertyClass = '\GeometriaLab\Model\Persistent\Schema\Property\ModelProperty';

    /**
     * Model class name
     *
     * @var string
     */
    static protected $modelClassName = 'GeometriaLab\Model\Persistent\AbstractModel';

    /**
     * Schema class name
     *
     * @var string
     */
    static protected $schemaClassName = 'GeometriaLab\Model\Persistent\Schema\Schema';

    /**
     * Parse class docblock
     *
     * @param string $className
     * @return Schema
     * @throws \InvalidArgumentException
     */
    protected function parseDocBlock($className)
    {
        $schema = parent::parseDocBlock($className);

        if ($schema->getMapperClass() === null) {
            throw new \InvalidArgumentException('Mapper method tag not present in docblock!');
        }

        /***
         * @var PropertyInterface $property
         */
        foreach($schema->getProperties() as $property) {
            if ($property->isPrimary()) {
                return $schema;
            }
        }

        throw new \InvalidArgumentException('Primary property (primary key) not present!');
    }

    /**
     * Parse method tag
     *
     * @param \Zend\Code\Reflection\DocBlock\Tag\MethodTag $tag
     * @param Schema $schema
     * @throws \InvalidArgumentException
     */
    protected function parseMethodTag(ZendMethodTag $tag, Schema $schema)
    {
        if ($tag->getMethodName() === 'getMapper()') {
            if (!$tag->isStatic()) {
                throw new \InvalidArgumentException('Mapper method tag in docblock must be static!');
            }

            if (!class_exists($tag->getReturnType())) {
                throw new \InvalidArgumentException('Invalid mapper class in mapper method tag in docblock!');
            }

            $params = $this->getParamsFromTag($tag);

            $schema->setMapperClass($tag->getReturnType());
            $schema->setMapperOptions($params);
        }
    }

    /**
     * Create property by type and params
     *
     * @static
     * @param string $type
     * @param array $params
     * @return PropertyInterface
     * @throws \InvalidArgumentException
     */
    static protected function createProperty($type, array $params = array())
    {
        if (isset(static::$regularPropertiesClassMap[$type])) {
            $property = new static::$regularPropertiesClassMap[$type]($params);
        } else if (class_exists($type)) {
            if (isset($params['relation'])) {
                if (!isset(static::$relationsClassMap[$params['relation']])) {
                    throw new \InvalidArgumentException("Invalid relation '{$params['relation']}'");
                }

                if (!isset($params['targetModelClass']) && $params['relation'] !== 'hasMany') {
                    $params['targetModelClass'] = $type;
                }

                $className = static::$relationsClassMap[$params['relation']];

                unset($params['relation']);
            } else {
                $params['modelClass'] = $type;

                $className = static::$modelPropertyClass;
            }

            $property = new $className($params);
        } else {
            throw new \InvalidArgumentException("Invalid property type '$type'");
        }

        return $property;
    }
}
