<?php

namespace GeometriaLabTest\Model\Schema\Property;

use GeometriaLab\Model\Schema\Property\ModelProperty;

class ModelPropertyTest extends \PHPUnit_Framework_TestCase
{
    public function testSetOptions()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Unknown property option \'foo\'');
        new ModelProperty(array('foo' => 'bar'));
    }

    public function testSetModelClass()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid model class, must be implements GeometriaLab\Model\Schemaless\ModelInterface');
        $m = new ModelProperty();
        $m->setModelClass('stdClass');
    }

    public function testSetAllowEmpty()
    {
        $model = new ModelProperty();
        $model->setRequired(true);
        $model->setAllowEmpty(false);

        $validators = $model->getValidatorChain()->getValidators();

        $this->assertInstanceOf('\Zend\Validator\NotEmpty', $validators[0]['instance']);
    }

    public function testRemoveAllowEmpty()
    {
        $model = new ModelProperty();
        $model->setRequired(true);
        $model->setAllowEmpty(false);

        $validators = $model->getValidatorChain()->getValidators();

        $this->assertInstanceOf('\Zend\Validator\NotEmpty', $validators[0]['instance']);

        $model->setAllowEmpty(true);

        foreach ($model->getValidatorChain()->getValidators() as $validator) {
            $this->assertNotInstanceOf('\Zend\Validator\NotEmpty', $validator['instance']);
        }
    }
}

