<?php

namespace GeometriaLabTest\Model\Persistent;

use GeometriaLabTest\Model\Persistent\Models\PersistentModel,
    GeometriaLabTest\Model\Persistent\Models\PersistentModelWithInvalidDefinition,
    GeometriaLabTest\Model\Persistent\Models\PersistentModelWithInvalidDefinition2,
    GeometriaLabTest\Model\Persistent\Models\PersistentModelWithInvalidDefinition3,
    GeometriaLabTest\Model\Persistent\Models\PersistentModelWithoutDefinition,
    GeometriaLabTest\Model\Models\SubModel;


class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Save model to storage
     *
     * @return boolean
     */
    public function save();

    /**
     * Delete model from storage
     *
     * @return boolean
     */
    public function delete();

    /**
     * Is not saved model
     *
     * @return boolean
     */
    public function isNew();

    /**
     * Is model changed
     *
     * @return boolean
     */
    public function isChanged();

    /**
     * Is property changed
     *
     * @param string $name
     * @return boolean
     */
    public function isPropertyChanged($name);

    /**
     * Get changed property
     *
     * @return array
     */
    public function getChangedProperties();

    /**
     * Get property change
     *
     * @param string $name
     * @return array
     */
    public function getChange($name);

    /**
     * Get model changes
     *
     * @return array
     */
    public function getChanges();

    /**
     * Get clean property value
     *
     * @param string $name
     * @return mixed
     */
    public function getClean($name);

    /**
     * Mark model as clean
     *
     * @param boolean $flag
     * @return ModelInterface
     */
    public function markClean($flag = true);

    /**
     * Get mapper
     *
     * @static
     * @return Mapper\MapperInterface
     */
    static public function getMapper();




    public function testGetMapper()
    {
        $mapper = PersistentModel::getMapper();
        $this->assertInstanceOf('\GeometriaLab\Model\Persistent\Models\MockMapper', $mapper);
    }

    public function testGetMapperWithoutDefinition()
    {
        $this->setExpectedException('\InvalidArgumentException');
        PersistentModelWithoutDefinition::getMapper();
    }

    public function testGetMapperWithInvalidDefinition()
    {
        $this->setExpectedException('\InvalidArgumentException');
        PersistentModelWithInvalidDefinition::getMapper();
    }

    public function testGetMapperWithInvalidDefinition2()
    {
        $this->setExpectedException('\InvalidArgumentException');
        PersistentModelWithInvalidDefinition2::getMapper();
    }

    public function testGetMapperWithInvalidDefinition3()
    {
        $this->setExpectedException('\InvalidArgumentException');
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