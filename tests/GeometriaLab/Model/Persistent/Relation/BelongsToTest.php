<?php

namespace GeometriaLabTest\Model\Persistent\Relation;

use GeometriaLabTest\Model\Persistent\Relation\TestModels\Man,
    GeometriaLabTest\Model\Persistent\Relation\TestModels\Dog;

class BelongsToTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTargetModelWithNullOriginProperty()
    {
        $dog = new Dog(array('name' => 'Lucky'));
        $this->assertNull($dog->man);
    }

    public function testSetTargetModel()
    {
        $dog = new Dog(array('name' => 'Lucky'));
        $man = new Man(array('name' => 'Lucky'));
        $dog->man = $man;
        $this->assertEquals($man, $dog->man);
    }
}