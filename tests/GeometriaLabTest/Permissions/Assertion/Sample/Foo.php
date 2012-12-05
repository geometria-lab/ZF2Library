<?php

namespace GeometriaLabTest\Permissions\Assertion\Sample;

use GeometriaLab\Permissions\Assertion\Assertion,
    GeometriaLab\Permissions\Assertion\Resource\AbstractResource;

class Foo extends AbstractResource
{
    public function canDynamicAssert(Assertion $assertion, \stdClass $obj, array $array)
    {
        return !empty($obj) && !empty($array);
    }
}
