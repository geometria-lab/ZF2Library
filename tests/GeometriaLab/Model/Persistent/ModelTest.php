<?php

namespace GeometriaLabTest\Model\Persistent;

use GeometriaLabTest\Model\Persistent\Models\PersistentModel,
    GeometriaLabTest\Model\Persistent\Models\PersistentModelWithInvalidDefinition,
    GeometriaLabTest\Model\Persistent\Models\PersistentModelWithInvalidDefinition2,
    GeometriaLabTest\Model\Persistent\Models\PersistentModelWithInvalidDefinition3,
    GeometriaLabTest\Model\Models\SubModel;


class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function testGetMapper()
    {
        $mapper = PersistentModel::getMapper();
        $this->assertInstanceOf('\GeometriaLab\Model\Persistent\Models\MockMapper', $mapper);
    }

    public function testGetMapperWithoutDefinition()
    {

    }

    public function testGetMapperWithInvalidDefinition()
    {
        PersistentModelWithInvalidDefinition::getMapper();
    }

    public function testGetMapperWithInvalidDefinition2()
    {
        PersistentModelWithInvalidDefinition2::getMapper();
    }

    public function testGetMapperWithInvalidDefinition3()
    {

        PersistentModelWithInvalidDefinition3::getMapper();
    }

    protected function getData()
    {
        return array(
            'booleanProperty' => true,
            'floatProperty'   => 3.4,
            'integerProperty' => 10,
            'stringProperty'  => 'test',
            'subTest'         => new PersistentModel(array('id' => 1, 'title' => 'Hello')),
            'arrayOfInteger'  => array(9, 10, 11, 12, 13),
            'arrayOfString'   => array('string1', 'string2'),
            'arrayOfSubTest'  => array(new SubModel(array('id' => 1, 'title' => 'Hello')), new SubModel(array('id' => 2, 'title' => 'Hello2')))
        );
    }
}