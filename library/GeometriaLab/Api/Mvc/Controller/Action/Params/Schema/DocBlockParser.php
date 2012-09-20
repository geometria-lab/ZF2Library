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
        'string'  => 'GeometriaLab\Model\Persistent\Schema\Property\StringProperty',
        'array'   => 'GeometriaLab\Model\Persistent\Schema\Property\ArrayProperty',
        'boolean' => 'GeometriaLab\Model\Persistent\Schema\Property\BooleanProperty',
        'float'   => 'GeometriaLab\Model\Persistent\Schema\Property\FloatProperty',
        'integer' => 'GeometriaLab\Model\Persistent\Schema\Property\IntegerProperty',
    );

    /**
     * Schema class name
     *
     * @var string
     */
    static protected $schemaClassName = 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Schema';
}
