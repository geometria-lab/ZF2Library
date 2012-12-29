<?php

namespace GeometriaLabTest\Model\Persistent;

use GeometriaLabTest\Mongo\AbstractTestCase,
    GeometriaLabTest\Model\Persistent\TestModels\Relations\Man,
    GeometriaLabTest\Model\Persistent\TestModels\Relations\Dog,
    GeometriaLabTest\Model\Persistent\TestModels\Relations\Woman;

class CollectionTest extends AbstractTestCase
{
    public function testFetchAllRelations()
    {
        $this->createModels();
        $this->createModels();

        $collection = Man::getMapper()->getAll();

        foreach ($collection as $model) {
            /* @var Man $model */
            $this->assertFalse($model->getRelation('dog')->hasTargetModel());
            $this->assertFalse($model->getRelation('women')->hasTargetModels());
        }

        $collection->fetchRelations();

        foreach ($collection as $model) {
            /* @var Man $model */
            $this->assertTrue($model->getRelation('dog')->hasTargetModel());
            $this->assertTrue($model->getRelation('women')->hasTargetModels());
        }
    }

    public function testFetchOneRelation()
    {
        $this->createModels();
        $this->createModels();

        $collection = Man::getMapper()->getAll();

        foreach ($collection as $model) {
            /* @var Man $model */
            $this->assertFalse($model->getRelation('dog')->hasTargetModel());
            $this->assertFalse($model->getRelation('women')->hasTargetModels());
        }

        $collection->fetchRelations(
            'dog'
        );

        foreach ($collection as $model) {
            /* @var Man $model */
            $this->assertTrue($model->getRelation('dog')->hasTargetModel());
            $this->assertFalse($model->getRelation('women')->hasTargetModels());
        }
    }

    public function testNestedHasOneRelation()
    {
        $this->createModels();
        $this->createModels();

        $collection = Man::getMapper()->getAll();

        foreach ($collection as $model) {
            /* @var Man $model */
            $this->assertFalse($model->getRelation('dog')->hasTargetModel());
        }

        $collection->fetchRelations(array(
            'dog' => 'man',
        ));

        foreach ($collection as $model) {
            /* @var Man $model */
            $this->assertTrue($model->getRelation('dog')->hasTargetModel());
            $this->assertTrue($model->get('dog')->getRelation('man')->hasTargetModel());
        }
    }

    public function testNestedBelongsToRelation()
    {
        $this->createModels();
        $this->createModels();

        $collection = Dog::getMapper()->getAll();

        foreach ($collection as $model) {
            /* @var Dog $model */
            $this->assertFalse($model->getRelation('man')->hasTargetModel());
        }

        $collection->fetchRelations(array(
            'man' => 'women',
        ));

        foreach ($collection as $model) {
            /* @var Dog $model */
            $this->assertTrue($model->getRelation('man')->hasTargetModel());
            $this->assertTrue($model->get('man')->getRelation('women')->hasTargetModels());

            foreach ($model->get('man')->get('women') as $woman) {
                $this->assertInstanceOf('\GeometriaLabTest\Model\Persistent\TestModels\Relations\Woman', $woman);
            }
        }
    }

    public function testNestedHasManyRelation()
    {
        $this->createModels();
        $this->createModels();

        $query = Woman::getMapper()->createQuery();
        $collection = Woman::getMapper()->getAll($query);

        foreach ($collection as $model) {
            /* @var Woman $model */
            $this->assertFalse($model->getRelation('man')->hasTargetModel());
        }

        $collection->fetchRelations(array(
            'man' => 'dog',
        ));

        foreach ($collection as $model) {
            /* @var Woman $model */
            $this->assertTrue($model->getRelation('man')->hasTargetModel());
            $this->assertTrue($model->get('man')->getRelation('dog')->hasTargetModel());
        }
    }

    public function testNotExistentRelation()
    {
        $this->setExpectedException("\\InvalidArgumentException", "Model doesn't have 'bad' relation");

        $this->createModels();

        $collection = Woman::getMapper()->getAll();
        $collection->fetchRelations('bad');
    }

    public function testBadChildRelationName()
    {
        $this->setExpectedException("\\InvalidArgumentException", "Child relation must be a string but integer is given.");

        $this->createModels();

        $collection = Woman::getMapper()->getAll();
        $collection->fetchRelations(array('man' => 1));
    }

    protected function createModels()
    {
        $philip = new Man(array(
            'name'  => 'Philip',
        ));
        $philip->save();

        $seymour = new Dog(array(
            'name'  => 'Seymour',
            'manId' => $philip->id,
        ));
        $seymour->save();

        $michelle = new Woman(array(
            'name'  => 'Michelle',
            'manId' => $philip->id,
        ));
        $michelle->save();

        $leela = new Woman(array(
            'name'  => 'Leela',
            'manId' => $philip->id,
        ));
        $leela->save();


    }
}