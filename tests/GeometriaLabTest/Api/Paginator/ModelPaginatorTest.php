<?php

namespace GeometriaLabTest\Api\Paginator;

use GeometriaLabTest\Api\Paginator\TestModels\PersistentModel;

use GeometriaLab\Api\Paginator\ModelPaginator,
    GeometriaLab\Model\Persistent\Collection;

class ModelPaginatorTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        PersistentModel::getMapper()->deleteByQuery(PersistentModel::getMapper()->createQuery());
    }

    public function testGetItemsCount()
    {
        $models = $this->createModels();

        $paginator = new ModelPaginator(PersistentModel::getMapper()->createQuery(), 3);

        $this->assertEquals(3, $paginator->count());
    }

    public function testGetOneItem()
    {
        $models = $this->createModels();

        $paginator = new ModelPaginator(PersistentModel::getMapper()->createQuery(), 1);

        $items = $paginator->getItems();

        $this->assertInstanceOf('\GeometriaLab\Model\Persistent\Collection', $items);

        $expectedCollection = new Collection($models[0]);

        $this->assertEquals($expectedCollection, $items);
    }

    public function testGetSeveralItems()
    {
        $models = $this->createModels();

        $paginator = new ModelPaginator(PersistentModel::getMapper()->createQuery(), 2);

        $items = $paginator->getItems();

        $expectedCollection = new Collection();
        $expectedCollection->push($models[0])
                           ->push($models[1]);

        $this->assertEquals($expectedCollection, $items);
    }

    public function testGetOffsetItems()
    {
        $models = $this->createModels();

        $paginator = new ModelPaginator(PersistentModel::getMapper()->createQuery(), 2, 1);

        $items = $paginator->getItems();

        $expectedCollection = new Collection();
        $expectedCollection->push($models[1])
                           ->push($models[2]);

        $this->assertEquals($expectedCollection, $items);
    }

    protected function createModels()
    {
        $model1 = new PersistentModel(array('id' => '1', 'name' => 'one'));
        $model1->save();

        $model2 = new PersistentModel(array('id' => '2', 'name' => 'two'));
        $model2->save();

        $model3 = new PersistentModel(array('id' => '3', 'name' => 'three'));
        $model3->save();

        return array($model1, $model2, $model3);
    }
}
