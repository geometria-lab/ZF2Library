<?php

namespace GeometriaLabTest\Model\Persistent\Relation;

use GeometriaLab\Model\Schema\Manager as SchemaManager;

use GeometriaLabTest\Model\Persistent\Relation\TestModels\Man,
    GeometriaLabTest\Model\Persistent\Relation\TestModels\Dog;

class HasOneTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        $schemaManager = SchemaManager::getInstance();
        $schemaManager->removeAll();

        Man::getMapper()->deleteAll();
        Dog::getMapper()->deleteAll();
    }

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


    public function testRemoveTargetRelationsWithOnDeleteEqualsSetNull()
    {
        $man = new Man(array('name' => 'Ivan'));
        $man->save();

        $dog = new Dog(array('name' => 'Lucky', 'manId' => $man->id));
        $dog->save();

        $man->delete();

        $newDog = Dog::getMapper()->get($dog->id);

        $this->assertNull($newDog->manId);
    }

    public function testRemoveTargetRelationsWithOnDeleteEqualsCascade()
    {
        $man = new Man(array('name' => 'Ivan'));
        $man->getSchema()->getProperty('dog')->setOnDelete('cascade');
        $man->save();

        $dog = new Dog(array('name' => 'Lucky', 'manId' => $man->id));
        $dog->save();

        $man->delete();

        $newDog = Dog::getMapper()->get($dog->id);

        $this->assertNull($newDog);
    }

    public function testRemoveTargetRelationsWithOnDeleteEqualsNone()
    {
        $man = new Man(array('name' => 'Ivan'));
        $man->getSchema()->getProperty('dog')->setOnDelete('none');
        $man->save();

        $dog = new Dog(array('name' => 'Lucky', 'manId' => $man->id));
        $dog->save();

        $man->delete();

        $newDog = Dog::getMapper()->get($dog->id);

        $this->assertNotNull($newDog->manId);
        $this->assertEquals($dog->manId, $newDog->manId);
        $this->assertEquals($dog, $newDog);
    }
}