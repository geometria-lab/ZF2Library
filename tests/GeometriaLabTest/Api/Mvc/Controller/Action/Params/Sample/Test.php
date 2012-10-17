<?php

namespace GeometriaLabTest\Api\Mvc\Controller\Action\Params\Sample;

use GeometriaLab\Api\Mvc\Controller\Action\Params\AbstractParams;

/**
 * @property integer $id {"required": true}
 * @property array $array {"required": true}
 * @property float $float
 * @property boolean $bool
 * @property string $name {"required": true, "filters": ["StringTrim"]}
 * @property string $email {"filters": ["StringTrim", {"name": "StringToLower"}], "validators": [{"name": "EmailAddress", "breakOnFailure": true}]}
 */
class Test extends AbstractParams
{

}