<?php

namespace GeometriaLabTest\Permissions\Assertion;

use GeometriaLab\Permissions\Assertion\Assertion;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $resource = new Sample\Foo('Foo');

        $this->assertEquals('Foo', $resource->getName());
    }
}
