<?php

namespace GeometriaLabTest\Mongo\Model\Relations;

use GeometriaLab\Mongo\Manager as MongoManager,
    GeometriaLab\Mongo\Model\Mapper,
    GeometriaLab\Model\Schema\Manager as SchemaManager;

use GeometriaLabTest\Mongo\Model\Relations\TestModels\Man,
    GeometriaLabTest\Mongo\Model\Relations\TestModels\Woman;

class OneToManyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Man
     */
    protected $man;

    /**
     * @var array
     */
    protected $women = array();

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

        $this->women['Ulyana'] = new Woman();
        $this->women['Ulyana']->name = 'Ulyana';
        $this->women['Ulyana']->manId = $this->man->id;
        $this->women['Ulyana']->save();

        $this->women['Marina'] = new Woman();
        $this->women['Marina']->name = 'Marina';
        $this->women['Marina']->manId = $this->man->id;
        $this->women['Marina']->save();

        $this->women['Alisa'] = new Woman();
        $this->women['Alisa']->name = 'Alisa';
        $this->women['Alisa']->manId = $this->man->id;
        $this->women['Alisa']->save();
    }

    public function tearDown()
    {
        $manager = MongoManager::getInstance();
        $mongoDb = $manager->get('default');
        $mongoDb->drop();

        $schemaManager = SchemaManager::getInstance();
        $schemaManager->removeAll();
    }

    public function testGetForeignModels()
    {
        $collection = $this->man->women;
        $this->assertInstanceOf('\GeometriaLab\Model\Persistent\Collection', $collection);

        $this->assertEquals(Woman::getMapper()->getAll(), $this->man->women);
    }

    public function testGetReferencedModel()
    {
        $this->assertEquals($this->man, $this->women['Ulyana']->man);
    }

    public function testSetForeignProperty()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->man->woman = $this->women['Ulyana'];
    }

    public function testDeleteReferencedModelWithOnDeleteEqualsSetNull()
    {
        $this->man->delete();

        $woman = Woman::getMapper()->get($this->women['Alisa']->id);

        $this->assertNull($woman->manId);
    }

    public function testDeleteReferencedModelWithOnDeleteEqualsCascade()
    {
        $this->man->getSchema()->getProperty('women')->setOnDelete('cascade');
        $this->man->delete();

        $woman = Woman::getMapper()->get($this->women['Alisa']->id);

        $this->assertNull($woman);
    }

    public function testDeleteReferencedModelWithOnDeleteEqualsNone()
    {
        $this->man->getSchema()->getProperty('women')->setOnDelete('none');
        $id = $this->man->id;
        $this->man->delete();

        $woman = Woman::getMapper()->get($this->women['Alisa']->id);

        $this->assertEquals($id, $woman->manId);
    }

    public function testFetchRelations()
    {
        $women = Woman::getMapper()->getAll();

        $this->assertFalse($women->getFirst()->has('man'));

        $women->fetchRelations('man');

        $this->assertTrue($women->getFirst()->has('man'));
    }
}