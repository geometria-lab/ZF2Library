<?php

namespace GeometriaLabTest\Mongo\Model;

use GeometriaLabTest\Mongo\Model\Models\Model,
    GeometriaLabTest\Model\Models\SubModel;

use GeometriaLab\Mongo\Manager,
    GeometriaLab\Mongo\Model\Mapper;

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

        $this->mapper = Model::getMapper();
    }

    public function tearDown()
    {
        $manager = Manager::getInstance();
        $mongoDb = $manager->get('default');
        $mongoDb->drop();
    }

    public function testCreateAndGet()
    {
        $model = new Model($this->getData());

        $this->assertTrue($this->mapper->create($model));

        $fetchedModel = $this->mapper->get($model->id);

        $this->assertEquals($model, $fetchedModel);
    }

    public function testGetNotPresent()
    {
        $this->assertNull($this->mapper->get("adsdasdsa"));
    }

    public function testGetAll()
    {
        $this->markTestIncomplete();
    }

    public function testCount()
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

    protected function getData()
    {
        return array(
            'floatProperty'   => 3.4,
            'integerProperty' => 10,
            'stringProperty'  => 'test',
            'subTest'         => new SubModel(array('id' => 1, 'title' => 'Hello')),
            'arrayOfInteger'  => array(9, 10, 11, 12, 13),
            'arrayOfString'   => array('string1', 'string2'),
            'arrayOfSubTest'  => array(new SubModel(array('id' => 1, 'title' => 'Hello')), new SubModel(array('id' => 2, 'title' => 'Hello2')))
        );
    }
}
