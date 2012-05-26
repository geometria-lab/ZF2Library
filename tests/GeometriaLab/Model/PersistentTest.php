<?php

namespace GeometriaLabTest\Model;

class PersistentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeometriaLabTest\Model\TestModels\Persistent
     */
    protected $model;

    public function setUp()
    {
        $this->model = new TestModels\Persistent();
    }

    public function testGetMapper()
    {
        $model = $this->model;
        $mapper = $model::getMapper();
        $this->assertInstanceOf('\GeometriaLab\Model\Persistent\Mapper\Mongo', $mapper);
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