<?php

namespace GeometriaLabTest\Model\TestModels;

/**
 * @property boolean                                       $booleanProperty
 * @property integer                                       $callbackProperty
 * @property float                                         $floatProperty
 * @property integer                                       $integerProperty
 * @property string                                        $stringProperty   {"defaultValue" : "default"}
 * @property \GeometriaLabTest\Model\TestModels\SubModel       $subTest
 * @property integer[]                                     $arrayOfInteger   {"defaultValue" : [1, 2, 3, 4,
 *                                                                                             5, 6, 7, 8]}
 * @property string[]                                      $arrayOfString
 * @property \GeometriaLabTest\Model\TestModels\SubModel[]     $arrayOfSubTest
 */
class Model extends \GeometriaLab\Model\AbstractModel
{
    protected $callbackProperty;

    public function getCallbackProperty()
    {
        return $this->callbackProperty;
    }

    public function setCallbackProperty($value)
    {
        $this->callbackProperty = $value;
    }
}