<?php

namespace GeometriaLabTest\Model\Persistent\Relations\TestModels;

use GeometriaLab\Model\Persistent\Model;

/**
 * @property string                                                         $id    {"primary" : true}
 * @property string                                                         $name
 * @property \GeometriaLabTest\Model\Persistent\Relations\TestModels\Woman  $woman { "relation" : "hasOne", "foreignProperty" : "manId" }
 *
 * @method static \GeometriaLab\Mongo\Model\Mapper getMapper() {"mongoInstanceName" : "default", "collectionName" : "man"}
 */
class Man extends Model
{

}
