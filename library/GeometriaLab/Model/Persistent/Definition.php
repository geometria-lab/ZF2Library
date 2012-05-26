<?php

namespace GeometriaLab\Model\Persistent;

use GeometriaLab\Model,
    GeometriaLab\Code\Reflection\DocBlock\Tag\MethodTag;

class Definition extends Model\Definition
{
    /**
     * Mapper tag
     *
     * @var MethodTag
     */
    protected $mapperTag;

    /**
     * Constructor
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
     * Create mapper from definition
     *
     * @return \GeometriaLab\Model\Persistent\Mapper\MapperInterface
     */
    public function createMapper()
    {
        $className = $this->mapperTag->getReturnType();

        return new $className($this->mapperTag->getParams());
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