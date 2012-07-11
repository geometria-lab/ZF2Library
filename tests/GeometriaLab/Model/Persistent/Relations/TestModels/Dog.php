<?php

namespace GeometriaLabTest\Model\Persistent\Relations\TestModels;

use GeometriaLab\Model\Persistent\Model;

/**
 * @property string                                                       $id    {"primary" : true}
 * @property string                                                       $name
 * @property string                                                       $manId
 * @property \GeometriaLabTest\Model\Persistent\Relations\TestModels\Man  $man { "relation" : "belongsTo", "foreignProperty" : "manId" }
 *
 * @method static \GeometriaLab\Mongo\Model\Mapper getMapper() {"mongoInstanceName" : "default", "collectionName" : "dogs"}
 */
class Dog extends Model
{

}
