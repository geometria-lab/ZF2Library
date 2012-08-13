<?php

namespace GeometriaLabTest\Model\Persistent\Relation\TestModels;

use GeometriaLab\Model\Persistent\Model;

/**
 * @property integer                                                    $id    {"primary" : true}
 * @property string                                                     $name
 * @property integer                                                    $manId
 * @property \GeometriaLabTest\Model\Persistent\Relation\TestModels\Man $man { "relation" : "belongsTo", "originProperty" : "manId" }
 *
 * @method static \GeometriaLab\Model\Persistent\Mapper\Mock getMapper()
 */
class Woman extends Model
{

}
