<?php

namespace GeometriaLabTest\Model\Persistent\TestModels\WithInvalidRelations;

/**
 * @property integer $id { "primary" : true }
 * @property \GeometriaLabTest\Model\TestModels\SubModel $foo
 *
 * @method static \GeometriaLab\Model\Persistent\Mapper\Mock getMapper()
 */
class NotRelation extends \GeometriaLabTest\Model\Persistent\TestModels\Model
{

}