<?php

namespace GeometriaLabTest\Model\Schema;

use GeometriaLab\Model\Schema\Manager,
    GeometriaLab\Model\Schema\DocBlockParser;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Manager::getInstance()->removeAll();
    }

    public function testClone()
    {
        $this->setExpectedException('\RuntimeException', 'Cloning of GeometriaLab\Model\Schema\Manager is forbidden. It is a singleton');
        $m = clone Manager::getInstance();
    }

    public function testAdd()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Model \'GeometriaLabTest\Model\TestModels\Model\' schema already added');
        $foo = DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\TestModels\Model');
        $bar = DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\TestModels\Model');
        Manager::getInstance()->add($foo);
        Manager::getInstance()->add($bar);
    }

    public function testGet()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Model \'GeometriaLabTest\Model\TestModels\Model\' schema not present');
        Manager::getInstance()->get('GeometriaLabTest\Model\TestModels\Model');
    }

    public function testGetAll()
    {
        $foo = DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\TestModels\Model');
        $bar = DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\TestModels\SubModel');
        Manager::getInstance()->add($foo);
        Manager::getInstance()->add($bar);
        $this->assertEquals(2, count(Manager::getInstance()->getAll()));
    }

    public function testGetIterator()
    {
        $foo = DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\TestModels\Model');
        $bar = DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\TestModels\SubModel');
        Manager::getInstance()->add($foo);
        Manager::getInstance()->add($bar);
        $this->assertEquals(true, Manager::getInstance()->getIterator() instanceof \ArrayIterator);
        $this->assertEquals(2, count(Manager::getInstance()->getIterator()));
    }

    public function testRemove()
    {
        $foo = DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\TestModels\Model');
        Manager::getInstance()->add($foo);
        $this->assertEquals(1, count(Manager::getInstance()->getAll()));
        Manager::getInstance()->remove('GeometriaLabTest\Model\TestModels\Model');
        $this->assertEquals(0, count(Manager::getInstance()->getAll()));
    }

    public function testRemoveNotPresent()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Model \'GeometriaLabTest\Model\TestModels\Model\' schema not present');
        Manager::getInstance()->remove('GeometriaLabTest\Model\TestModels\Model');
    }
}

