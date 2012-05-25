<?php

namespace GeometriaLab\Model\Definition\Property;

use GeometriaLab\Model\Definition;

class Factory
{
    /**
     * Properties class map
     *
     * @var array
     */
    protected static $defaultPropertiesClassMap = array(
        'string'  => 'GeometriaLab\Model\Definition\Property\StringProperty',
        'array'   => 'GeometriaLab\Model\Definition\Property\ArrayProperty',
        'boolean' => 'GeometriaLab\Model\Definition\Property\BooleanProperty',
        'float'   => 'GeometriaLab\Model\Definition\Property\FloatProperty',
        'integer' => 'GeometriaLab\Model\Definition\Property\IntegerProperty',
    );

    /**
     * Create property by type and params
     *
     * @static
     * @param string $type
     * @param array $params
     * @return PropertyInterface
     * @throws \InvalidArgumentException
     */
    public static function factory($type, array $params = array())
    {
        if (isset(static::$defaultPropertiesClassMap[$type])) {
            $className = static::$defaultPropertiesClassMap[$type];

            $property = new $className($params);
        } else if (class_exists($type)) {
            $definitions = Definition\Manager::getInstance();
            if (!$definitions->has($type)) {
                $reflection = new \ReflectionClass($type);
                if ($reflection->isSubclassOf('GeometriaLab\Model\ModelInterface')) {
                    $definitions->add($type);
                }
            }

            $params['modelDefinition'] = $definitions->get($type);

            $property = new ModelProperty($params);
        } else {
            throw new \InvalidArgumentException("Invalid property type '$type'");
        }

        return $property;
    }
}
