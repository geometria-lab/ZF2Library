<?php

namespace GeometriaLabTest\Model\Schema;

use GeometriaLab\Model\Schema;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testClone()
    {
        $this->setExpectedException('\RuntimeException', 'Cloning of GeometriaLab\Model\Schema\Manager is forbidden. It is a singleton');
        $m = clone Schema\Manager::getInstance();
    }

    public function testAdd()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Model \'GeometriaLabTest\Model\TestModels\Model\' schema already added');
        $foo = new Schema\Schema('GeometriaLabTest\Model\TestModels\Model');
        $bar = new Schema\Schema('GeometriaLabTest\Model\TestModels\Model');
        Schema\Manager::getInstance()->add($foo);
        Schema\Manager::getInstance()->add($bar);
    }

    public function testGet()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Model \'GeometriaLabTest\Model\TestModels\Model\' schema not present');
        Schema\Manager::getInstance()->get('GeometriaLabTest\Model\TestModels\Model');
    }

    public function testGetAll()
    {
        $foo = new Schema\Schema('GeometriaLabTest\Model\TestModels\Model');
        $bar = new Schema\Schema('GeometriaLabTest\Model\TestModels\SubModel');
        Schema\Manager::getInstance()->add($foo);
        Schema\Manager::getInstance()->add($bar);
        $this->assertEquals(2, count(Schema\Manager::getInstance()->getAll()));
    }

    public function testGetIterator()
    {
        $foo = new Schema\Schema('GeometriaLabTest\Model\TestModels\Model');
        $bar = new Schema\Schema('GeometriaLabTest\Model\TestModels\SubModel');
        Schema\Manager::getInstance()->add($foo);
        Schema\Manager::getInstance()->add($bar);
        $this->assertEquals(true, Schema\Manager::getInstance()->getIterator() instanceof \ArrayIterator);
        $this->assertEquals(2, count(Schema\Manager::getInstance()->getIterator()));
    }

    public function testRemove()
    {
        $foo = new Schema\Schema('GeometriaLabTest\Model\TestModels\Model');
        Schema\Manager::getInstance()->add($foo);
        $this->assertEquals(1, count(Schema\Manager::getInstance()->getAll()));
        Schema\Manager::getInstance()->remove('GeometriaLabTest\Model\TestModels\Model');
        $this->assertEquals(0, count(Schema\Manager::getInstance()->getAll()));
    }

    public function testRemoveNotPresent()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Model \'GeometriaLabTest\Model\TestModels\Model\' schema not present');
        Schema\Manager::getInstance()->remove('GeometriaLabTest\Model\TestModels\Model');
    }

    public function setUp()
    {
        Schema\Manager::getInstance()->removeAll();
    }
}

