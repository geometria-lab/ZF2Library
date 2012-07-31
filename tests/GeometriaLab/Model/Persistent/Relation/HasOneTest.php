<?php

namespace GeometriaLabTest\Model\Persistent\Relation;

use GeometriaLabTest\Model\Persistent\Relation\TestModels\Man,
    GeometriaLabTest\Model\Persistent\Relation\TestModels\Dog;

class HasOneTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTargetModelWithoutRelations()
    {
        $man = new Man(array('name' => 'Ivan'));
        $this->assertNull($man->dog);
    }

    public function testSetTargetModel()
    {
        $man = new Man(array('name' => 'Ivan'));
        $dog = new Dog(array('name' => 'Lucky'));

        $man->dog = $dog;
        $this->assertEquals($dog, $man->dog);
    }
}