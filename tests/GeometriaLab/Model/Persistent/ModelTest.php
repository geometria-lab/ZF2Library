<?php

namespace GeometriaLabTest\Model\Persistent;

use GeometriaLabTest\Model\Persistent\Models\Model,
    GeometriaLabTest\Model\Persistent\Models\ModelWithInvalidDefinition,
    GeometriaLabTest\Model\Persistent\Models\ModelWithInvalidDefinition2,
    GeometriaLabTest\Model\Persistent\Models\ModelWithoutDefinition,
    GeometriaLabTest\Model\Models\SubModel;


class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testSave()
    {
        $model = new Model();

        $model->populate($this->getData());

        // create if new
        $this->assertNull($model->id);
        $this->assertTrue($model->save());
        $this->assertNotNull($model->id);

        $newModel = $model::getMapper()->get($model->id);

        $this->assertEquals($newModel, $model);

        // update if changed
        $model->set('integerProperty', 11);

        $this->assertTrue($model->save());

        $newModel = $model::getMapper()->get($model->id);

        $this->assertEquals($newModel, $model);

        // no changes - nothing to do
        $this->assertFalse($model->save());
    }

    public function testDelete()
    {
        $model = new Model();

        $model->populate($this->getData())
              ->save();

        $this->assertTrue($model->delete());

        $this->assertTrue($model->isNew());

        $this->assertNull($model::getMapper()->get($model->id));
    }

    public function testDeleteNotSaved()
    {
        $model = new Model();

        $model->populate($this->getData());
        $this->assertFalse($model->delete());
    }

    public function testIsNew()
    {
        $model = new Model();

        $model->populate($this->getData());
        $this->assertTrue($model->isNew());

        $model->save();

        $this->assertFalse($model->isNew());
    }

    public function testIsChanged()
    {
        $model = new Model();

        $model->populate($this->getData());
        $this->assertTrue($model->isChanged());

        $model->save();

        $this->assertFalse($model->isChanged());

        $model->set('integerProperty', 11);

        $this->assertTrue($model->isChanged());
    }

    public function testIsPropertyChanged()
    {
        $model = new Model();

        $model->populate($this->getData())
              ->save();

        $this->assertFalse($model->isPropertyChanged('integerProperty'));

        $model->set('integerProperty', 11);

        $this->assertTrue($model->isPropertyChanged('integerProperty'));
    }

    public function testGetChangedProperties()
    {
        $model = new Model();

        $model->populate($this->getData())
              ->save();

        $model->set('floatProperty', 11.0);
        $model->set('integerProperty', 11);


        $this->assertEquals(array('floatProperty', 'integerProperty'), $model->getChangedProperties());
    }

    public function testGetChange()
    {
        $model = new Model();

        $model->populate($this->getData())
            ->save();

        $model->set('integerProperty', 11);

        $this->assertEquals(array(10, 11), $model->getChange('integerProperty'));
    }

    public function testGetChanges()
    {
        $model = new Model();

        $model->populate($this->getData())
            ->save();

        $model->set('integerProperty', 11);
        $model->set('floatProperty', 11.0);

        $changes = array('integerProperty' => array(10, 11), 'floatProperty' => array(3.4, 11.0));

        $this->assertEquals($changes, $model->getChanges());
    }

    public function testGetClean()
    {
        $model = new Model();

        $model->populate($this->getData())
              ->save();

        $model->set('integerProperty', 11);

        $this->assertEquals(10, $model->getClean('integerProperty'));
    }

    public function testGetMapper()
    {
        $mapper = Model::getMapper();
        $this->assertInstanceOf('\GeometriaLabTest\Model\Persistent\Models\MockMapper', $mapper);
    }

    public function testGetMapperWithoutDefinition()
    {
        $this->setExpectedException('\InvalidArgumentException');
        ModelWithoutDefinition::getMapper();
    }

    public function testGetMapperWithInvalidDefinition()
    {
        $this->setExpectedException('\InvalidArgumentException');
        ModelWithInvalidDefinition::getMapper();
    }

    public function testGetMapperWithInvalidDefinition2()
    {
        $this->setExpectedException('\InvalidArgumentException');
        ModelWithInvalidDefinition2::getMapper();
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