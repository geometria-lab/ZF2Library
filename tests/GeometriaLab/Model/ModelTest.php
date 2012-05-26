<?php

namespace GeometriaLabTest\Model;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeometriaLabTest\Model\TestModels\Model
     */
    protected $model;

    public function setUp()
    {
        $this->model = new TestModels\Model();
    }

    public function testPropertyDefaultValue()
    {
        $this->assertEquals('default', $this->model->stringProperty);
        $this->assertEquals(array(1, 2, 3, 4, 5, 6, 7, 8), $this->model->arrayOfInteger);
    }

    public function testGet()
    {
        var_dump(TestModels\Model::createDefinition());
        var_dump(TestModels\SubModel::createDefinition());


        $data = $this->getData();
        $this->model->populate($data);

        $this->assertEquals(true, $this->model->booleanProperty);
        $this->assertEquals(3.4, $this->model->floatProperty);
        $this->assertEquals($data['subTest'], $this->model->subTest);
    }

    public function testGetNotPresentProperty()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->model->undefinedProperty;
    }

    public function testSet()
    {
        $this->model->booleanProperty = false;
        $this->assertEquals(false, $this->model->booleanProperty);
    }

    public function testSetNotPresentProperty()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->model->undefinedProperty = 1;
    }

    public function testSetInvalidDataToBoolean()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->model->booleanProperty = 1;
    }

    public function testSetInvalidDataToFloat()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->model->floatProperty = 'a';
    }

    public function testSetInvalidDataToInteger()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->model->integerProperty = 'a';
    }

    public function testSetInvalidDataToString()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->model->stringProperty = 1;
    }

    public function testSetInvalidDataToSubTest()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->model->subTest = 4;
    }

    public function testSetInvalidDataToArrayOfInteger()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->model->arrayOfInteger = array(true, 1, 3);
    }

    public function testSetInvalidDataToArrayOfString()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->model->arrayOfString = 1;
    }

    public function testSetInvalidDataToArrayOfSubTest()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->model->arrayOfSubTest = array(new TestModels\SubModel(array('id' => 1, 'title' => 'Hello')), array('tsa' => 123));
    }

    public function setArrayToModelProperty()
    {
        $this->model->subTest = array('id' => 1, 'title' => 'Hello');
        $this->assertEquals(new TestModels\SubModel(array('id' => 1, 'title' => 'Hello')), $this->model->subTest);
    }

    protected function getData()
    {
        return array(
            'booleanProperty' => true,
            'floatProperty'   => 3.4,
            'integerProperty' => 10,
            'stringProperty'  => 'test',
            'subTest'         => new TestModels\SubModel(array('id' => 1, 'title' => 'Hello')),
            'arrayOfInteger'  => array(9, 10, 11, 12, 13),
            'arrayOfString'   => array('string1', 'string2'),
            'arrayOfSubTest'  => array(new TestModels\SubModel(array('id' => 1, 'title' => 'Hello')), new TestModels\SubModel(array('id' => 2, 'title' => 'Hello2')))
        );
    }
}