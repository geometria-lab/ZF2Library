<?php

namespace GeometriaLab\Model\Persistent;

use GeometriaLab\Code\Reflection\DocBlock\Tag\MethodTag,
    GeometriaLab\Model\Schema\Property\PropertyInterface;

class Schema extends \GeometriaLab\Model\Schema
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
     * Primary property names
     *
     * @var array
     */
    protected $primaryPropertyNames = array();

    /**
     * Protected constructor
     *
     * @param string $className
     */
    public function __construct($className = null)
    {
        if (!static::getTagManager()->hasTag('method')) {
            static::getTagManager()->addTagPrototype(new MethodTag());
        }

        parent::__construct($className);
    }

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
     * Get primary property names
     *
     * @return array
     */
    public function getPrimaryPropertyNames()
    {
        return $this->primaryPropertyNames;
    }

    /**
     * Set primary property names
     *
     * @param array $names
     * @return Schema
     */
    public function setPrimaryPropertyNames(array $names)
    {
        $this->primaryPropertyNames = $names;

        return $this;
    }

    /**
     * Create and set property
     *
     * @param string $name
     * @param string $type
     * @param array $params
     * @return PropertyInterface
     */
    protected function createAndSetProperty($name, $type, array $params = array())
    {
        if (isset($params['primary'])) {
            if ($params['primary']) {
                $this->primaryPropertyNames[] = $name;
            }

            unset($params['primary']);
        }

        return parent::createAndSetProperty($name, $type, $params);
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

        if (empty($this->primaryPropertyNames)) {
            throw new \InvalidArgumentException('Primary property (primary key) not present!');
        }
    }

    /**
     * Parse method tag
     *
     * @param MethodTag $tag
     * @throws \InvalidArgumentException
     */
    protected function parseMethodTag(MethodTag $tag)
    {
        if ($tag->getMethodName() === 'getMapper()') {
            if (!$tag->isStatic() || !class_exists($tag->getReturnType()) || $tag->getParams() === array()) {
                throw new \InvalidArgumentException('Invalid mapper method tag in docblock!');
            }

            $this->setMapperClass($tag->getReturnType());
            $this->setMapperOptions($tag->getParams());
        }
    }


}