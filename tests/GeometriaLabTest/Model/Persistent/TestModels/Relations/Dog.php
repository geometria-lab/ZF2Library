<?php

namespace GeometriaLabTest\Model\Persistent\TestModels\Relations;

use GeometriaLab\Model\Persistent\AbstractModel;

/**
 * @property string                                                         $id     {"primary": true}
 * @property string                                                         $name
 * @property string                                                         $manId
 * @property \GeometriaLabTest\Model\Persistent\TestModels\Relations\Man    $man    {"relation": "belongsTo", "originProperty": "manId"}
 *
 * @method static \GeometriaLab\Mongo\Model\Mapper getMapper() {"mongoInstanceName": "default", "collectionName": "dog"}
 */
class Dog extends AbstractModel
{

}
