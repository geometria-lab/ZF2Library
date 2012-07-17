<?php

namespace GeometriaLab\Model\Persistent\Schema;

use GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface;

use Zend\Code\Reflection\DocBlock\Tag\MethodTag as ZendMethodTag;

class Schema extends \GeometriaLab\Model\Schema\Schema
{
    /**
     * Mapper class
     *
     * @var string
     */
    protected $mapperClass;

    /**
     * Mapper params
     *
     * @var array
     */
    protected $mapperOptions = array();

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
     * Set mapper class
     *
     * @param string $mapperClass
     */
    public function setMapperClass($mapperClass)
    {
        $this->mapperClass = $mapperClass;
    }

    /**
     * Get mapper class
     *
     * @return string
     */
    public function getMapperClass()
    {
        return $this->mapperClass;
    }

    /**
     * Set mapper params
     *
     * @param array $mapperOptions
     */
    public function setMapperOptions($mapperOptions)
    {
        $this->mapperOptions = $mapperOptions;
    }

    /**
     * Get mapper
     *
     * @return array
     */
    public function getMapperOptions()
    {
        return $this->mapperOptions;
    }

    /**
     * Parse class docblock
     *
     * @param string $className
     * @throws \InvalidArgumentException
     */
    protected function parseDocblock($className)
    {
        parent::parseDocblock($className);

        if ($this->mapperClass === null) {
            throw new \InvalidArgumentException('Mapper method tag not present in docblock!');
        }

        /***
         * @var PropertyInterface $property
         */
        foreach($this->getProperties() as $property) {
            if ($property->isPrimary()) {
                return;
            }
        }

        throw new \InvalidArgumentException('Primary property (primary key) not present!');
    }

    /**
     * Parse method tag
     *
     * @param ZendMethodTag $tag
     * @throws \InvalidArgumentException
     */
    protected function parseMethodTag(ZendMethodTag $tag)
    {
        if ($tag->getMethodName() === 'getMapper()') {
            if (!$tag->isStatic()) {
                throw new \InvalidArgumentException('Mapper method tag in docblock must be static!');
            }

            if (!class_exists($tag->getReturnType())) {
                throw new \InvalidArgumentException('Invalid mapper class in mapper method tag in docblock!');
            }

            $params = $this->getParamsFromTag($tag);

            $this->setMapperClass($tag->getReturnType());
            $this->setMapperOptions($params);
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

                if (!isset($params['modelClass']) && $params['relation'] !== 'hasMany') {
                    $params['modelClass'] = $type;
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