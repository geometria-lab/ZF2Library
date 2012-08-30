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
}

