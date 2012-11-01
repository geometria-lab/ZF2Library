<?php

namespace GeometriaLabTest\Api\Mvc\Controller\Action\Params\Sample;

use GeometriaLab\Api\Mvc\Controller\Action\Params\AbstractParams;

/**
 * @property string     $id                 {"required": true}
 * @property array      $array              {"required": true}
 * @property float      $float
 * @property boolean    $bool
 * @property string     $name               {"required": true, "filters": ["StringTrim"]}
 * @property string     $email              {"filters": ["StringTrim", {"name": "StringToLower"}],
 *                                           "validators": [{"name": "EmailAddress", "breakOnFailure": true}]}
 * @property string     $defaultProperty    {"defaultValue": "Bar"}
 *
 * @property \GeometriaLabTest\Api\Mvc\Controller\Action\Params\TestModel\TestModel $relationModel {"required": true,
 *                                                                                                  "relation": "belongsTo",
 *                                                                                                  "originProperty": "id"}
 */
class Test extends AbstractParams
{

}