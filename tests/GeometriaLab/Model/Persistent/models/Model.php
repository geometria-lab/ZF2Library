<?php

namespace GeometriaLabTest\Model\Persistent\Models;

/**
 * @property integer                                      $id               { "primary" : true }
 * @property float                                        $floatProperty
 * @property integer                                      $integerProperty
 * @property string                                       $stringProperty   {"defaultValue" : "default"}
 * @property \GeometriaLabTest\Model\Models\SubModel      $subTest
 * @property integer[]                                    $arrayOfInteger   {"defaultValue" : [1, 2, 3, 4,
 *                                                                                             5, 6, 7, 8]}
 * @property string[]                                     $arrayOfString
 * @property \GeometriaLabTest\Model\Models\SubModel[]    $arrayOfSubTest
 *
 * @method static \GeometriaLabTest\Model\Persistent\Models\MockMapper getMapper()
 */
class Model extends \GeometriaLab\Model\Persistent\Model
{

}