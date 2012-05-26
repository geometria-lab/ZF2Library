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
        $className = get_called_class();

        $definitions = Definition\Manager::getInstance();

        if ($definitions->has($className)) {
            $definition = $definitions->get($className);
        } else {

        }


        if (static::$mapper === null) {



            static::$mapper = static::$definition->createMapper();
        }

        return static::$mapper;
    }

    /**
     * Create persistent model definition
     *
     * @return Persistent\Definition
     */
    public static function createDefinition()
    {
        $className = get_called_class();

        return new Persistent\Definition($className);
    }
}