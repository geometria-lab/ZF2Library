<?php

namespace GeometriaLabTest\Mongo\Model\TestModels;

/**
 * @property string                                       $id               { "primary" : true }
 * @property float                                        $floatProperty
 * @property integer                                      $integerProperty
 * @property string                                       $stringProperty   { "defaultValue" : "default"}
 * @property \GeometriaLabTest\Model\TestModels\SubModel      $subTest
 * @property integer[]                                    $arrayOfInteger   { "defaultValue" : [1, 2, 3, 4,
 *                                                                                             5, 6, 7, 8]}
 * @property string[]                                     $arrayOfString
 * @property \GeometriaLabTest\Model\TestModels\SubModel[]    $arrayOfSubTest
 *
 * @method static \GeometriaLab\Mongo\Model\Mapper getMapper() { "mongoInstanceName" : "default", "collectionName" : "test" }
 */
class Model extends \GeometriaLab\Model\Persistent\AbstractModel
{

}