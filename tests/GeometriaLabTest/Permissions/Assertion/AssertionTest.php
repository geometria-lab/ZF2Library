<?php

namespace GeometriaLabTest\Permissions\Assertion;

use GeometriaLab\Permissions\Assertion\Assertion;

class AssertionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddResource()
    {
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $this->assertInstanceOf('\\GeometriaLab\\Permissions\\Assertion\\ResourceInterface', $assertion->getResource('Foo'));
        $this->assertEquals($fooResource, $assertion->getResource('Foo'));
    }

    public function testAddResources()
    {
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $barResource = new Sample\Bar('Bar');
        $assertion->addResource($barResource);

        $expected = array(
            'Foo' => $fooResource,
            'Bar' => $barResource,
        );

        $this->assertEquals($expected, $assertion->getResources());
    }

    public function testAddExistingResource()
    {
        $this->setExpectedException('\\GeometriaLab\\Permissions\\Assertion\\Exception\\InvalidArgumentException');
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);
        $assertion->addResource($fooResource);
    }

    public function testHasResourceByName()
    {
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $this->assertTrue($assertion->hasResource('Foo'));
    }

    public function testHasResourceByObject()
    {
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $this->assertTrue($assertion->hasResource($fooResource));
    }

    public function testHasNotExistingResource()
    {
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $this->assertFalse($assertion->hasResource('Bar'));
    }

    public function testGetResourceByName()
    {
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $this->assertEquals($fooResource, $assertion->getResource('Foo'));
    }

    public function testGetResourceByObject()
    {
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $this->assertEquals($fooResource, $assertion->getResource($fooResource));
    }

    public function testGetNotExistingResource()
    {
        $this->setExpectedException('\\GeometriaLab\\Permissions\\Assertion\\Exception\\InvalidArgumentException');

        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $assertion->getResource('Bar');
    }

    public function testRemoveResourceByName()
    {
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $this->assertEquals($fooResource, $assertion->getResource('Foo'));

        $assertion->removeResource('Foo');

        $this->assertFalse($assertion->hasResource('Foo'));
        $this->assertEmpty($assertion->getResources());
    }

    public function testAssertWithoutResource()
    {
        $this->setExpectedException('\\GeometriaLab\\Permissions\\Assertion\\Exception\\InvalidArgumentException');

        $assertion = new Assertion();
        $assertion->assert('Bar', 'privilege');
    }

    public function testAssertWithoutPrivilege()
    {
        $this->setExpectedException('\\GeometriaLab\\Permissions\\Assertion\\Exception\\RuntimeException');

        $assertion = new Assertion();
        $barResource = new Sample\Bar('Bar');
        $assertion->addResource($barResource);

        $assertion->assert('Bar', 'privilege');
    }

    public function testAssertWithAllowedPrivilege()
    {
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $this->assertTrue($assertion->assert('Foo', 'allowedForAll'));
    }

    public function testWithDynamicAssert()
    {
        $assertion = new Assertion();
        $fooResource = new Sample\Foo('Foo');
        $assertion->addResource($fooResource);

        $obj = new \stdClass();
        $obj->bar = 'bar';

        $this->assertTrue($assertion->assert('Foo', 'dynamicAssert', $obj, array('bar')));
    }
}
