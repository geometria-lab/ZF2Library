<?php

namespace GeometriaLabTest\Mongo\Model\Relations;

use GeometriaLab\Mongo\Manager as MongoManager,
    GeometriaLab\Mongo\Model\Mapper,
    GeometriaLab\Model\Schema\Manager as SchemaManager;

use GeometriaLabTest\Mongo\Model\Relations\TestModels\Man,
    GeometriaLabTest\Mongo\Model\Relations\TestModels\Dog;

class OneToOneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Man
     */
    protected $man;

    /**
     * @var Dog
     */
    protected $dog;

    public function setUp()
    {
        $manager = MongoManager::getInstance();
        if (!$manager->has('default')) {
            $mongo = new \Mongo(TESTS_MONGO_MAPPER_CONNECTION_SERVER);
            $mongoDb = $mongo->selectDB(TESTS_MONGO_MAPPER_CONNECTION_DB);
            $manager->set('default', $mongoDb);
        }

        $this->man = new Man();
        $this->man->name = 'Ivan';
        $this->man->save();

        $this->dog = new Dog();
        $this->dog->name = 'Lucky';
        $this->dog->manId = $this->man->id;
        $this->dog->save();
    }

    public function tearDown()
    {
        $manager = MongoManager::getInstance();
        $mongoDb = $manager->get('default');
        $mongoDb->drop();

        $schemaManager = SchemaManager::getInstance();
        $schemaManager->removeAll();
    }

    public function testGetForeignModel()
    {
        $this->assertEquals($this->dog, $this->man->dog);
    }

    public function testGetReferencedModel()
    {
        $this->assertEquals($this->man, $this->dog->man);
    }

    public function testSetForeignModel()
    {
        $this->markTestIncomplete();

        $newMan = new Man();
        $newMan->name = 'John';
        $newMan->save();

        $this->dog->man = $newMan;

        //$this->assertEquals(array('manId' => array($this->man->id, $newMan->id)), $this->dog->getChanges());

        $this->assertEquals($newMan, $this->dog->man);
    }

    public function testSetForeignProperty()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->man->dog = $this->dog;
    }

    public function testSetReferencedModel()
    {
        $this->markTestIncomplete();

        $newMan = new Man();
        $newMan->name = 'John';
        $newMan->save();

        $this->dog->manId = $newMan->id;

        //$this->assertEquals(array('manId' => array($this->man->id, $newMan->id)), $this->dog->getChanges());

        $this->assertEquals($newMan, $this->dog->man);
    }

    public function testSetNullToForeignProperty()
    {
        $this->markTestIncomplete();

        $this->dog->manId = null;

        //$this->assertEquals(array('manId' => array($this->man->id, null)), $this->dog->getChanges());

        $this->assertNull($this->dog->man);

        $this->dog->save();

        $dog = Dog::getMapper()->get($this->dog->id);

        $this->assertNull($dog->man);
    }

    public function testSetNullToForeignRelation()
    {
        $this->markTestIncomplete();

        $this->dog->man = null;

        //$this->assertEquals(array('manId' => array($this->man->id, null)), $this->dog->getChanges());

        $this->assertNull($this->dog->man);

        $this->dog->save();

        $dog = Dog::getMapper()->get($this->dog->id);

        $this->assertNull($dog->man);
    }

    public function testSetModelWithoutReferencedPropertyToForeignRelation()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $newMan = new Man();
        $newMan->name = 'John';

        $this->dog->man = $newMan;
    }

    public function testDeleteReferencedModelWithOnDeleteEqualsSetNull()
    {
        $this->man->delete();

        $dog = Dog::getMapper()->get($this->dog->id);

        $this->assertNull($dog->manId);
    }

    public function testDeleteReferencedModelWithOnDeleteEqualsCascade()
    {
        $this->man->getSchema()->getProperty('dog')->setOnDelete('cascade');
        $this->man->delete();

        $dog = Dog::getMapper()->get($this->dog->id);

        $this->assertNull($dog);
    }

    public function testDeleteReferencedModelWithOnDeleteEqualsNone()
    {
        $this->man->getSchema()->getProperty('dog')->setOnDelete('none');
        $id = $this->man->id;
        $this->man->delete();

        $dog = Dog::getMapper()->get($this->dog->id);

        $this->assertEquals($id, $dog->manId);
    }
}