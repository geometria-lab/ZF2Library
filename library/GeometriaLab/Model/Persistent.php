<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Persistent\Mapper\MapperInterface;

abstract class Persistent extends Model
{
    /**
     * Mapper
     *
     * @var MapperInterface
     */
    protected static $mapper;

    /**
     *
     *
     * @var string
     */
    protected static $definitionClass = '\GeometriaLab\Model\Persistent\Definition';

    /**
     * Set mapper
     *
     * @static
     * @param MapperInterface $mapper
     */
    public static function setMapper(MapperInterface $mapper)
    {
        static::$mapper = $mapper;
    }

    /**
     * Get mapper
     *
     * @static
     * @return MapperInterface
     */
    public static function getMapper()
    {
        if (static::$mapper === null) {
            static::$mapper = static::$definition->createMapper();
        }

        return static::$mapper;
    }

    /**
     * Create persistent model definition
     *
     * @param string $className
     * @return Persistent\Definition
     */
    protected function createDefinition($className)
    {
        return new Persistent\Definition($className);
    }
}