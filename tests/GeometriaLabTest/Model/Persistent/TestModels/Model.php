<?php

namespace GeometriaLabTest\Model\Persistent\TestModels;

/**
 * @property integer                                      $id               { "primary" : true }
 * @property float                                        $floatProperty
 * @property integer                                      $integerProperty
 * @property string                                       $stringProperty   {"defaultValue" : "default"}
 * @property \GeometriaLabTest\Model\TestModels\SubModel      $subTest
 * @property integer[]                                    $arrayOfInteger   {"defaultValue" : [1, 2, 3, 4,
 *                                                                                             5, 6, 7, 8]}
 * @property string[]                                     $arrayOfString
 * @property \GeometriaLabTest\Model\TestModels\SubModel[]    $arrayOfSubTest
 *
 * @method static \GeometriaLab\Model\Persistent\Mapper\Mock getMapper()
 */
class Model extends \GeometriaLab\Model\Persistent\AbstractModel
{

}