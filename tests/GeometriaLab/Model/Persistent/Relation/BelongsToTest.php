<?php

namespace GeometriaLabTest\Model\Persistent\Relation;

use GeometriaLabTest\Model\Persistent\Relation\TestModels\Man,
    GeometriaLabTest\Model\Persistent\Relation\TestModels\Dog;

class BelongsToTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Man::getMapper()->deleteAll();
        Dog::getMapper()->deleteAll();
    }

    public function testGetTargetModelWithNullOriginProperty()
    {
        $dog = new Dog(array('name' => 'Lucky'));
        $this->assertNull($dog->man);
    }

    public function testSetNotSavedTargetModel()
    {
        $this->setExpectedException('InvalidArgumentException');

        $man = new Man(array('name' => 'Ivan'));
        $dog = new Dog(array('name' => 'Lucky'));
        $dog->man = $man;
    }

    public function testSetTargetModel()
    {
        $man = new Man(array('name' => 'Ivan'));
        $man->save();

        $dog = new Dog(array('name' => 'Lucky'));
        $dog->man = $man;

        $this->assertEquals($man->id, $dog->manId);
        $this->assertEquals($man, $dog->man);

        $dog->man = null;

        $this->assertNull($dog->manId);
        $this->assertNull($dog->man);
    }
}