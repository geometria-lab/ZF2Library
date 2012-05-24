<?php

namespace GeometriaLabTest\Model\TestModels;

use GeometriaLab\Model\Model;

/**
 * @property boolean        $booleanProperty
 * @property float          $floatProperty
 * @property integer        $integerProperty
 * @property string         $stringProperty   {"defaultValue" : "default"}
 * @property SubTest        $subTest
 * @property array(integer) $arrayOfInteger   {"defaultValue" : [1, 2, 3, 4
 *                                                               5, 6, 7, 8]}
 * @property array(string)  $arrayOfString
 * @property array(SubTest) $arrayOfSubTest
 */
class Test extends Model
{

}