<?php

namespace GeometriaLab\Model\Persistent;

use GeometriaLab\Code\Reflection\DocBlock\Tag\MethodTag,
    GeometriaLab\Model\Definition\Property\PropertyInterface;

class Definition extends \GeometriaLab\Model\Definition
{
    /**
     * Mapper tag
     *
     * @var MethodTag
     */
    protected $mapperTag;

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
    public function __construct($className)
    {
        if (!static::getTagManager()->hasTag('method')) {
            static::getTagManager()->addTagPrototype(new MethodTag());
        }

        parent::__construct($className);
    }

    /**
     * Create and set property
     *
     * @param string $name
     * @param string $type
     * @param array $params
     * @return PropertyInterface
     */
    public function createAndSetProperty($name, $type, array $params = array())
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
     * Create mapper from definition
     *
     * @return Mapper\MapperInterface
     */
    public function createMapper()
    {
        $className = $this->mapperTag->getReturnType();

        $params = $this->mapperTag->getParams();

        $params['modelClass'] = $this->getClassName();

        return new $className($params);
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
     * @return Definition
     */
    public function setPrimaryPropertyNames(array $names)
    {
        $this->primaryPropertyNames = $names;

        return $this;
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

        if ($this->mapperTag === null) {
            throw new \InvalidArgumentException('Mapper definition not present!');
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
                throw new \InvalidArgumentException('Invalid mapper definition!');
            }

            $this->mapperTag = $tag;
        }
    }
}