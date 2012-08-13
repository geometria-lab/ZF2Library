<?php

namespace GeometriaLabTest\Model\Persistent\Relation;

use GeometriaLab\Model\Schema\Manager as SchemaManager;

use GeometriaLabTest\Model\Persistent\Relation\TestModels\Man,
    GeometriaLabTest\Model\Persistent\Relation\TestModels\Woman;

class HasManyTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        $schemaManager = SchemaManager::getInstance();
        $schemaManager->removeAll();

        Woman::getMapper()->deleteAll();
        Man::getMapper()->deleteAll();
    }

    public function testGetTargetModels()
    {
        $man = new Man(array('name' => 'Ivan'));
        $man->save();

        $this->assertInstanceOf('\GeometriaLab\Model\Persistent\Collection', $man->women);
        $this->assertTrue($man->women->isEmpty());

        $woman = new Woman(array('name' => 'Alisa', 'manId' => $man->id));
        $woman->save();
        $woman = new Woman(array('name' => 'Ulyana', 'manId' => $man->id));
        $woman->save();
        $woman = new Woman(array('name' => 'Marina', 'manId' => $man->id));
        $woman->save();

        $women = Woman::getMapper()->getAll();

        $this->assertEquals($women, $man->getRelation('women')->getTargetModels(true));
    }

    public function testSetTargetModels()
    {
        $woman = new Woman(array('name' => 'Alisa'));
        $woman->save();
        $woman = new Woman(array('name' => 'Ulyana'));
        $woman->save();
        $woman = new Woman(array('name' => 'Marina'));
        $woman->save();

        $women = Woman::getMapper()->getAll();

        $man = new Man(array('name' => 'Ivan'));
        $man->women = $women;

        $this->assertInstanceOf('\GeometriaLab\Model\Persistent\Collection', $man->women);
        $this->assertEquals($women, $man->women);
    }

    public function testRemoveTargetRelationsWithOnDeleteEqualsSetNull()
    {
        $man = new Man(array('name' => 'Ivan'));
        $man->save();

        $woman = new Woman(array('name' => 'Alisa', 'manId' => $man->id));
        $woman->save();
        $woman = new Woman(array('name' => 'Ulyana', 'manId' => $man->id));
        $woman->save();
        $woman = new Woman(array('name' => 'Marina', 'manId' => $man->id));
        $woman->save();

        $man->delete();

        $women = Woman::getMapper()->getAll();

        $this->assertCount(3, $women);

        foreach($women as $woman) {
            $this->assertNull($woman->manId);
        }
    }

    public function testRemoveTargetRelationsWithOnDeleteEqualsCascade()
    {
        $man = new Man(array('name' => 'Ivan'));
        $man->getSchema()->getProperty('women')->setOnDelete('cascade');
        $man->save();

        $woman = new Woman(array('name' => 'Alisa', 'manId' => $man->id));
        $woman->save();
        $woman = new Woman(array('name' => 'Ulyana', 'manId' => $man->id));
        $woman->save();
        $woman = new Woman(array('name' => 'Marina', 'manId' => $man->id));
        $woman->save();

        $man->delete();

        $women = Woman::getMapper()->getAll();

        $this->assertTrue($women->isEmpty());
    }

    public function testRemoveTargetRelationsWithOnDeleteEqualsNone()
    {
        $man = new Man(array('name' => 'Ivan'));
        $man->getSchema()->getProperty('women')->setOnDelete('none');
        $man->save();

        $woman = new Woman(array('name' => 'Alisa', 'manId' => $man->id));
        $woman->save();
        $woman = new Woman(array('name' => 'Ulyana', 'manId' => $man->id));
        $woman->save();
        $woman = new Woman(array('name' => 'Marina', 'manId' => $man->id));
        $woman->save();

        $man->delete();

        $women = Woman::getMapper()->getAll();

        $this->assertCount(3, $women);

        foreach($women as $newWoman) {
            $this->assertEquals($woman->manId, $newWoman->manId);
        }
    }
}