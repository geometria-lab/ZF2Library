<?php

namespace GeometriaLabTest\Model\Persistent\Relation\TestModels;

use GeometriaLab\Model\Persistent\Model;

/**
 * @property integer                                                    $id    { "primary" : true }
 * @property string                                                     $name
 *
 * @property \GeometriaLab\Model\Persistent\Collection                  $women { "relation"         : "hasMany",
 *                                                                               "targetProperty"   : "manId",
 *                                                                               "targetModelClass" : "\\GeometriaLabTest\\Model\\Persistent\\Relation\\TestModels\\Woman" }
 *
 * @property \GeometriaLabTest\Model\Persistent\Relation\TestModels\Dog $dog   { "relation"       : "hasOne",
 *                                                                               "targetProperty" : "manId" }
 *
 * @method static \GeometriaLab\Model\Persistent\Mapper\Mock getMapper()
 */
class Man extends Model
{

}
