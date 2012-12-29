<?php

namespace GeometriaLabTest\Model\Persistent\TestModels\Relations;

use GeometriaLab\Model\Persistent\AbstractModel;

/**
 * @property string                                                         $id     {"primary": true}
 * @property string                                                         $name
 *
 * @property \GeometriaLabTest\Model\Persistent\TestModels\Relations\Dog    $dog    {"relation": "hasOne", "targetProperty": "manId"}
 * @property \GeometriaLab\Model\Persistent\Collection                      $women  {"relation": "hasMany",
 *                                                                                  "targetProperty": "manId",
 *                                                                                  "targetModelClass": "\\GeometriaLabTest\\Model\\Persistent\\TestModels\\Relations\\Woman"}
 *
 * @method static \GeometriaLab\Mongo\Model\Mapper getMapper() {"mongoInstanceName": "default", "collectionName": "man"}
 */
class Man extends AbstractModel
{

}
