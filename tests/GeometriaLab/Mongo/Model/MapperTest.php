<?php

namespace GeometriaLabTest\Mongo\Model;

use GeometriaLabTest\Mongo\Model\Models\Model,
    GeometriaLabTest\Model\Models\SubModel;

use GeometriaLab\Mongo\Manager,
    GeometriaLab\Mongo\Model\Mapper;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $manager = Manager::getInstance();
        if (!$manager->has('default')) {
            $mongo = new \Mongo(TESTS_MONGO_MAPPER_CONNECTION_SERVER);
            $mongoDb = $mongo->selectDB(TESTS_MONGO_MAPPER_CONNECTION_DB);
            $manager->set('default', $mongoDb);
        }
    }

    public function tearDown()
    {
        $manager = Manager::getInstance();
        $mongoDb = $manager->get('default');
        $mongoDb->drop();
    }

    public function testGet()
    {
        $model = new Model();
        $model->set('floatProperty', 2.5);
        $model->save();

        $fetchedModel = Model::getMapper()->get($model->id);

        $this->assertEquals($model, $fetchedModel);
        $this->assertEquals(2.5, $fetchedModel->get('floatProperty'));
    }

    public function testGetNotPresent()
    {
        $model = Model::getMapper()->get("adsdasdsa");
        $this->assertNull($model);
    }

    public function testGetAll()
    {
        $model = new Model();
        $model->floatProperty = 1.0;
        $model->integerProperty = 1;
        $model->save();

        $model2 = new Model();
        $model2->floatProperty = 1.0;
        $model2->integerProperty = 2;
        $model2->save();

        $model3 = new Model();
        $model3->floatProperty = 1.2;
        $model3->integerProperty = 3;
        $model3->save();

        $model4 = new Model();
        $model4->floatProperty = 1.0;
        $model4->integerProperty = 4;
        $model4->save();

        $model5 = new Model();
        $model5->floatProperty = 1.0;
        $model5->integerProperty = 5;
        $model5->save();

        $collection = Model::getMapper()->getAll();
        $this->assertCount(5, $collection);

        $query = Model::getMapper()->createQuery();
        $query->select(array('integerProperty' => true))
              ->where(array('floatProperty' => 1.0))
              ->sort('integerProperty', false)
              ->limit(3)
              ->offset(1);

        $collection = Model::getMapper()->getAll($query);
        $this->assertInstanceOf('GeometriaLab\Model\Persistent\Collection', $collection);

        $this->assertCount(3, $collection);

        foreach($collection as $model) {
            $this->assertFalse($model->has('floatProperty'));
        }

        $this->assertEquals($model4->id, $collection[0]->id);


        $this->assertEquals($model2->id, $collection[1]->id);

        $this->assertEquals($model->id, $collection[2]->id);
    }

    public function testGetAllByInteger()
    {
        $model = new Model();
        $model->integerProperty = 1;
        $model->save();

        $this->assertModelByCondition($model, array('integerProperty' => 1));
    }

    public function testGetAllByString()
    {
        $model = new Model();
        $model->stringProperty = "test";
        $model->save();

        $this->assertModelByCondition($model, array('stringProperty' => "test"));
    }

    public function testGetAllByIntegerInArray()
    {
        $model = new Model();
        $model->arrayOfInteger = array(1, 2, 3);
        $model->save();

        $this->assertModelByCondition($model, array('arrayOfInteger' => 1));
        $this->assertModelByCondition($model, array('arrayOfInteger' => array('$in' => array(2))));
    }

    public function testGetAllByStringInArray()
    {
        $model = new Model();
        $model->arrayOfString = array('dasd', 'dsadda', 'dasdsadas');
        $model->save();

        $condition = array('arrayOfString' => array('$in' => array('dasdsadas', 'aaaa')));
        $this->assertModelByCondition($model, $condition);

        $this->assertModelByCondition($model, array('arrayOfString' => 'dasd'));
    }

    public function testGetAllBySubModel()
    {
        $model = new Model();
        $model->subTest = new SubModel(array('id' => 1, 'title' => 'Hello'));
        $model->save();

        $condition = array('subTest' => array('id' => 1, 'title' => 'Hello'));
        $this->assertModelByCondition($model, $condition);

        $this->assertModelByCondition($model, array('subTest.id' => 1));
    }

    public function testGetAllBySubModelInArray()
    {
        $model = new Model();
        $model->arrayOfSubTest = array(
            new SubModel(array('id' => 1, 'title' => 'Hello')),
            new SubModel(array('id' => 2, 'title' => 'Hello2')),
            new SubModel(array('id' => 2, 'title' => 'Hello3')),
        );
        $model->save();

        $condition = array('arrayOfSubTest' => array('id' => 1, 'title' => 'Hello'));
        $this->assertModelByCondition($model, $condition);

        $this->assertModelByCondition($model, array('arrayOfSubTest.0.id' => 1));
        $this->assertModelByCondition($model, array('arrayOfSubTest.id' => 1));
    }

    public function testCount()
    {
        $model = new Model();
        $model->floatProperty = 1.0;
        $model->integerProperty = 1;
        $model->save();

        $model2 = new Model();
        $model2->floatProperty = 1.0;
        $model2->integerProperty = 2;
        $model2->save();

        $model3 = new Model();
        $model3->floatProperty = 1.2;
        $model3->integerProperty = 3;
        $model3->save();

        $count = Model::getMapper()->count();
        $this->assertEquals(3, $count);

        $count = Model::getMapper()->count(array('floatProperty' => 1.2));
        $this->assertEquals(1, $count);
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

    protected function assertModelByCondition(Model $model, array $condition)
    {
        $fetchedModel = Model::getMapper()->getByCondition($condition);
        $this->assertEquals($model, $fetchedModel);
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
