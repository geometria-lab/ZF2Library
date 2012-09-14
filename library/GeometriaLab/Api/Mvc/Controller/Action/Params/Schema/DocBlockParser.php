<?php
namespace GeometriaLab\Api\Mvc\Controller\Action\Params\Schema;

use GeometriaLab\Model\Schema\Schema,
    GeometriaLab\Model\Schema\DocBlockParser as ModelDocBlockParser,
    GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\PropertyInterface;

use Zend\Code\Reflection\DocBlock\Tag\MethodTag as ZendMethodTag;

class DocBlockParser extends ModelDocBlockParser
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
}