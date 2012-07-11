<?php

namespace GeometriaLabTest\Model\Persistent\Relations\TestModels;

use GeometriaLab\Model\Persistent\Model;

/**
 * @property string                                                       $id    {"primary" : true}
 * @property string                                                       $name
 *
 * @property \GeometriaLab\Model\Persistent\Collection                    $women { "relation"        : "hasMany",
 *                                                                                 "foreignProperty" : "manId",
 *                                                                                 "modelClass"      : "\\GeometriaLabTest\\Model\\Persistent\\Relations\\TestModels\\Woman" }
 *
 * @property \GeometriaLabTest\Model\Persistent\Relations\TestModels\Dog  $dog   { "relation" : "hasOne",
 *                                                                                 "foreignProperty" : "manId" }
 *
 * @method static \GeometriaLab\Mongo\Model\Mapper getMapper() {"mongoInstanceName" : "default", "collectionName" : "men"}
 */
class Man extends Model
{

}
