<?php

/**
 * @property boolean                                      $booleanProperty
 * @property float                                        $floatProperty
 * @property integer                                      $integerProperty
 * @property string                                       $stringProperty   {"defaultValue" : "default"}
 * @property \GeometriaLabTest\Model\TestModels\SubModel   $subTest
 * @property integer[]                                    $arrayOfInteger   {"defaultValue" : [1, 2, 3, 4,
 *                                                                                             5, 6, 7, 8]}
 * @property string[]                                     $arrayOfString
 * @property \GeometriaLabTest\Model\TestModels\SubModel[] $arrayOfSubTest
 */
class PersistentWithoutDefinition extends \GeometriaLab\Model\Persistent
{

}
