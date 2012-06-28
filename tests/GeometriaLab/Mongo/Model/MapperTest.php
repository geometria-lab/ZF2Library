<?php

namespace GeometriaLabTest\Mongo\Model;

use GeometriaLab\Mongo\Model\Mapper,
    GeometriaLab\Mongo\Manager;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mapper
     */
    protected $mapper;

    public function setUp()
    {
        $manager = Manager::getInstance();
        if (!$manager->has('default')) {
            $mongo = new \Mongo(TESTS_MONGO_MAPPER_CONNECTION_SERVER);
            $mongoDb = $mongo->selectDB(TESTS_MONGO_MAPPER_CONNECTION_DB);
            $manager->set('default', $mongoDb);
        }

        $this->mapper = new Mapper();
        $this->mapper->setCollectionName('test');
        $this->mapper->setMongoInstanceName('default');
        $this->mapper->setModelClass('GeometriaLabTest\Model\Persistent\Models\PersistentModel');
    }

    public function tearDown()
    {
        $manager = Manager::getInstance();
        $mongoDb = $manager->get('default');
        $mongoDb->drop();
    }

    public function testGet()
    {
        $this->markTestIncomplete();
    }

    public function testGetAll()
    {
        $this->markTestIncomplete();
    }

    public function testCount()
    {
        $this->markTestIncomplete();
    }

    public function testCreate()
    {
        $this->markTestIncomplete();
    }

    public function testUpdate()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateByCondition()
    {
        $this->markTestIncomplete();
    }

    public function testDelete()
    {
        $this->markTestIncomplete();
    }

    public function testDeleteByCondition()
    {
        $this->markTestIncomplete();
    }
}
