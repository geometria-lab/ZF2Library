<?php

namespace GeometriaLab\Model;

use GeometriaLab\Model\Persistent\Mapper;

abstract class Persistent extends Model
{
    public function save()
    {

    }

    /**
     * Get mapper
     *
     * @static
     * @return Persistent\Mapper\MapperInterface
     */
    public static function getMapper()
    {
        /**
         * @var Persistent $className
         */
        $className = get_called_class();

        $mappers = Mapper\Manager::getInstance();

        if (!$mappers->has($className)) {
            $definitions = Definition\Manager::getInstance();
            if (!$definitions->has($className)) {
                $className::createDefinition();
            }

            /**
             * @var Persistent\Definition $definition
             */
            $definition = $definitions->get($className);

            $mappers->add($className, $definition->createMapper());
        }

        return $mappers->get($className);
    }

    /**
     * Create persistent model definition
     *
     * @return Persistent\Definition
     */
    public static function createDefinition()
    {
        $definitions = Definition\Manager::getInstance();

        $className = get_called_class();

        if (!$definitions->has($className)) {
            $definitions->add(new Persistent\Definition($className));
        }

        return $definitions->get($className);
    }
}