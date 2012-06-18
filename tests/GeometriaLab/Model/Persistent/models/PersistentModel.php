<?php

namespace GeometriaLabTest\Model\Persistent\Models;

/**
 * @property boolean                                      $booleanProperty
 * @property float                                        $floatProperty
 * @property integer                                      $integerProperty
 * @property string                                       $stringProperty   {"defaultValue" : "default"}
 * @property \GeometriaLabTest\Model\Models\SubModel   $subTest
 * @property integer[]                                    $arrayOfInteger   {"defaultValue" : [1, 2, 3, 4,
 *                                                                                             5, 6, 7, 8]}
 * @property string[]                                     $arrayOfString
 * @property \GeometriaLabTest\Model\Models\SubModel[] $arrayOfSubTest
 *
 * @method static \GeometriaLab\Mongo\Model\Mapper getMapper()  {"collectionName" : "test"}
 */
class PersistentModel extends \GeometriaLab\Model\Persistent\Model
{

}