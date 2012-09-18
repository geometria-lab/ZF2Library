<?php
namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema;

use GeometriaLab\Model\Schema\DocBlockParser as ModelDocBlockParser;

class DocBlockParser extends ModelDocBlockParser
{
    /**
     * Regular properties class map
     *
     * @var array
     */
    static protected $regularPropertiesClassMap = array(
        'array'   => 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\ArrayProperty',
        'string'  => 'GeometriaLab\Model\Schema\Property\StringProperty',
        'boolean' => 'GeometriaLab\Model\Schema\Property\BooleanProperty',
        'float'   => 'GeometriaLab\Model\Schema\Property\FloatProperty',
        'integer' => 'GeometriaLab\Model\Schema\Property\IntegerProperty',
    );
}