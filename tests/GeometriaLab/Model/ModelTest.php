<?php

namespace GeometriaLabTest\Model;

use GeometriaLab\Model\Model;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeometriaLabTest\Model\TestModels\Test
     */
    protected $model;

    public function setUp()
    {
        $this->model = new TestModels\Test();
    }

    public function testPopulate()
    {
        $this->model->populate($this->getData());

        $this->assertEquals($this->getData(), $this->model->toArray());
    }

    public function testPopulateWithInvalidArgument()
    {
        $this->markTestIncomplete();
    }

    public function testDefaultValue()
    {
        $this->markTestIncomplete();
    }

    public function testGet()
    {
        $this->markTestIncomplete();
    }

    public function testSet()
    {
        $this->markTestIncomplete();
    }

    protected function getData()
    {
        return array(
            'booleanProperty' => true,
            'floatProperty'   => 3.4,
            'integerProperty' => 10,
            'stringProperty'  => 'test',
            'subTest'         => new TestModels\SubTest(array('id' => 1, 'title' => 'Hello')),
            'arrayOfInteger'  => array(9, 10, 11, 12, 13),
            'arrayOfString'   => array('string1', 'string2'),
            'arrayOfSubTest'  => array(new TestModels\SubTest(array('id' => 1, 'title' => 'Hello')), new TestModels\SubTest(array('id' => 2, 'title' => 'Hello2')))
        );
    }
}