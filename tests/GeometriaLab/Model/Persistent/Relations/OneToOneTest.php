<?php

namespace GeometriaLabTest\Model\Persistent\Relations;

use GeometriaLab\Mongo\Manager,
    GeometriaLab\Mongo\Model\Mapper;

use GeometriaLabTest\Model\Persistent\Relations\TestModels\Man,
    GeometriaLabTest\Model\Persistent\Relations\TestModels\Woman;

class OneToOneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Man
     */
    protected $man;

    /**
     * @var Woman
     */
    protected $woman;

    public function setUp()
    {
        $manager = Manager::getInstance();
        if (!$manager->has('default')) {
            $mongo = new \Mongo(TESTS_MONGO_MAPPER_CONNECTION_SERVER);
            $mongoDb = $mongo->selectDB(TESTS_MONGO_MAPPER_CONNECTION_DB);
            $manager->set('default', $mongoDb);
        }

        $this->man = new Man();
        $this->man->name = 'Adam';
        $this->man->save();

        $this->woman = new Woman();
        $this->woman->name = 'Eva';
        $this->woman->manId = $this->man->id;
        $this->woman->save();
    }

    public function tearDown()
    {
        $manager = Manager::getInstance();
        $mongoDb = $manager->get('default');
        $mongoDb->drop();
    }

    public function testGetForeignModel()
    {
        $this->assertEquals($this->woman, $this->man->woman);
    }

    public function testGetReferencedModel()
    {
        $this->assertEquals($this->man, $this->woman->man);
    }

    public function testSetForeignModel()
    {
        $newMan = new Man();
        $newMan->name = 'John';
        $newMan->save();

        $this->woman->man = $newMan;

        $this->assertEquals(array('manId' => array($this->man->id, $newMan->id)), $this->woman->getChanges());

        $this->assertEquals($newMan, $this->woman->man);
    }

    public function testSetForeignProperty()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->man->woman = $this->woman;
    }

    public function testSetReferencedModel()
    {
        $newMan = new Man();
        $newMan->name = 'John';
        $newMan->save();

        $this->woman->manId = $newMan->id;

        $this->assertEquals(array('manId' => array($this->man->id, $newMan->id)), $this->woman->getChanges());

        $this->assertEquals($newMan, $this->woman->man);
    }

    public function testSetNullToForeignProperty()
    {
        $this->woman->manId = null;

        $this->assertEquals(array('manId' => array($this->man->id, null)), $this->woman->getChanges());

        $this->assertNull($this->woman->man);

        $this->woman->save();

        $woman = Woman::getMapper()->get($this->woman->id);

        $this->assertNull($woman->man);
    }

    public function testSetNullToForeignRelation()
    {
        $this->woman->man = null;

        $this->assertEquals(array('manId' => array($this->man->id, null)), $this->woman->getChanges());

        $this->assertNull($this->woman->man);

        $this->woman->save();

        $woman = Woman::getMapper()->get($this->woman->id);

        $this->assertNull($woman->man);
    }

    public function testSetModelWithoutReferencedPropertyToForeignRelation()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $newMan = new Man();
        $newMan->name = 'John';

        $this->woman->man = $newMan;
    }

    public function testDeleteReferencedModelWithOnDeleteEqualsSetNull()
    {
        $this->man->delete();

        $woman = Woman::getMapper()->get($this->woman->id);

        $this->assertNull($woman->manId);
    }

    public function testDeleteReferencedModelWithOnDeleteEqualsCascade()
    {
        $this->man->getSchema()->getProperty('woman')->setOnDelete('cascade');
        $this->man->delete();

        $woman = Woman::getMapper()->get($this->woman->id);

        $this->assertNull($woman);
    }

    public function testDeleteReferencedModelWithOnDeleteEqualsNone()
    {
        $this->man->getSchema()->getProperty('woman')->setOnDelete('none');
        $id = $this->man->id;
        $this->man->delete();

        $woman = Woman::getMapper()->get($this->woman->id);

        $this->assertEquals($id, $woman->manId);
    }
}