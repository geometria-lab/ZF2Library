<?php

namespace GeometriaLabTest\Mongo\Model\Relations\TestModels;

use GeometriaLab\Model\Persistent\Model;

/**
 * @property string                                                       $id    {"primary" : true}
 * @property string                                                       $name
 * @property string                                                       $manId
 * @property \GeometriaLabTest\Mongo\Model\Relations\TestModels\Man  $man { "relation" : "belongsTo", "foreignProperty" : "manId" }
 *
 * @method static \GeometriaLab\Mongo\Model\Mapper getMapper() {"mongoInstanceName" : "default", "collectionName" : "women"}
 */
class Woman extends Model
{

}
