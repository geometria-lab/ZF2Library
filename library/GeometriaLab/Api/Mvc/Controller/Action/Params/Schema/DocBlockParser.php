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
     * Schema class name
     *
     * @var string
     */
    static protected $schemaClassName = 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Schema';
}
