<?php
namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema;

class DocBlockParser extends \GeometriaLab\Model\Schema\DocBlockParser
{
    /**
     * Regular properties class map
     *
     * @var array
     */
    static protected $regularPropertiesClassMap = array(
        'string'  => 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\StringProperty',
        'array'   => 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\ArrayProperty',
        'boolean' => 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\BooleanProperty',
        'float'   => 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\FloatProperty',
        'integer' => 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\IntegerProperty',
    );

    /**
     * Relations class map
     *
     * @var array
     */
    static protected $relationsClassMap = array(
        'belongsTo' => 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\Relation\BelongsTo',
    );

    /**
     * Schema class name
     *
     * @var string
     */
    static protected $schemaClassName = 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Schema';

    /**
     * Create property by type and params
     *
     * @static
     * @param string $type
     * @param array $params
     * @return \GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\PropertyInterface
     * @throws \InvalidArgumentException
     */
    static protected function createProperty($type, array $params = array())
    {
        if (isset(static::$regularPropertiesClassMap[$type])) {
            $property = new static::$regularPropertiesClassMap[$type]($params);
        } elseif (class_exists($type) && isset($params['relation'])) {
            if (!isset(static::$relationsClassMap[$params['relation']])) {
                throw new \InvalidArgumentException("Invalid relation '{$params['relation']}'");
            }

            if (!isset($params['targetModelClass'])) {
                $params['targetModelClass'] = $type;
            }

            $className = static::$relationsClassMap[$params['relation']];

            unset($params['relation']);

            $property = new $className($params);
        } else {
            throw new \InvalidArgumentException("Invalid property type '$type'");
        }

        return $property;
    }
}
