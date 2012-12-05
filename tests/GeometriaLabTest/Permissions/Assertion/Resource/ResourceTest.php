<?php

namespace GeometriaLabTest\Permissions\Assertion\Resource;

use GeometriaLabTest\Permissions\Assertion\SampleResource\Foo;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $resource = new Foo('Foo');

        $this->assertEquals('Foo', $resource->getName());
    }
}
