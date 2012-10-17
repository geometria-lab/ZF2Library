<?php

namespace GeometriaLabTest\Model;

use GeometriaLabTest\Model\TestModels\Model,
    GeometriaLabTest\Model\TestModels\SubModel,
    GeometriaLabTest\Model\TestModels\InheritModel;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    protected $model;

    public function setUp()
    {
        $this->model = new Model();
    }

    public function testPropertyDefaultValue()
    {
        $this->assertEquals('default', $this->model->stringProperty);
        $this->assertEquals(array(1, 2, 3, 4, 5, 6, 7, 8), $this->model->arrayOfInteger);
    }

    public function testGet()
    {
        $data = $this->getData();
        $this->model->populate($data);

        $this->assertEquals(true, $this->model->booleanProperty);
        $this->assertEquals(3.4, $this->model->floatProperty);
        $this->assertEquals($data['subTest'], $this->model->subTest);
        $this->assertEquals(null, $this->model->nonSetProperty);
    }

    public function testPopulateInvalidProperty()
    {
        $this->setExpectedException('GeometriaLab\Model\Schema\Property\Validator\Exception\InvalidValueException');

        $data = $this->getData();
        $data['booleanProperty'] = 'foo';
        $this->model->populate($data);
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
        $this->model->booleanProperty = 'foo';
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
        $this->model->arrayOfSubTest = array(new SubModel(array('id' => 1, 'title' => 'Hello')), array('tsa' => 123));
    }

    public function testSetArrayToModelProperty()
    {
        $this->model->subTest = array('id' => 1, 'title' => 'Hello');
        $this->assertEquals(new SubModel(array('id' => 1, 'title' => 'Hello')), $this->model->subTest);
    }

    public function testSettersAndGetters()
    {
        $this->model->callbackProperty = 1;
        $this->assertEquals(1,  $this->model->getCallbackProperty());
        $this->assertEquals(1,  $this->model->callbackProperty);
    }

    public function testHas()
    {
        $this->assertEquals(true, $this->model->has('booleanProperty'));
        $this->assertEquals(false, $this->model->has('nonExistsProperty'));
    }

    public function testHasInheritProperty()
    {
        $inheritModel = new InheritModel();
        $this->assertTrue($inheritModel->has('stringProperty'));
    }

    public function testGetDefaultInheritProperty()
    {
        $inheritModel = new InheritModel();
        $this->assertEquals('default', $inheritModel->stringProperty);
    }

    public function testGetFilteredProperty()
    {
        $this->model->trimmedProperty = ' need trim ';
        $this->assertEquals('need trim', $this->model->trimmedProperty);
    }

    public function testSetValidValidationProperty()
    {
        $this->model->emailProperty = 'email@example.com';
        $this->assertEquals('email@example.com', $this->model->emailProperty);
    }

    public function testSetInvalidValidationProperty()
    {
        $this->setExpectedException('GeometriaLab\Model\Schema\Property\Validator\Exception\InvalidValueException');
        $this->model->emailProperty = 'invalid email';
    }

    public function testRequiredProperty()
    {
        $data = $this->getData();
        unset($data['requiredProperty']);

        $this->model->populate($data);

        $this->assertFalse($this->model->isValid());

        $errorMessage = $this->model->getErrorMessages();

        $this->assertEquals($errorMessage, array('requiredProperty' => array('isRequired' => 'Value is required')));
    }

    public function testNotEmptyProperty()
    {
        $this->setExpectedException('GeometriaLab\Model\Schema\Property\Validator\Exception\InvalidValueException');

        $this->model->requiredProperty = '';
    }

    public function testEmptyProperty()
    {
        $this->model->emptyProperty = '';

        $this->assertEquals($this->model->emptyProperty, '');
    }

    protected function getData()
    {
        return array(
            'booleanProperty'  => true,
            'floatProperty'    => 3.4,
            'integerProperty'  => 10,
            'stringProperty'   => 'test',
            'subTest'          => new SubModel(array('id' => 1, 'title' => 'Hello')),
            'arrayOfInteger'   => array(9, 10, 11, 12, 13),
            'arrayOfString'    => array('string1', 'string2'),
            'arrayOfSubTest'   => array(new SubModel(array('id' => 1, 'title' => 'Hello')), new SubModel(array('id' => 2, 'title' => 'Hello2'))),
            'emailProperty'    => 'email@example.com',
            'requiredProperty' => 'test',
        );
    }
}