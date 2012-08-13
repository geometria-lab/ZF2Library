<?php

namespace GeometriaLabTest\Model\Schema;

use GeometriaLab\Model\Schema\Schema,
    GeometriaLab\Model\Model,
    GeometriaLabTest\Model\TestModels\WithInvalidSchema\DoubleProperty;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPropertyNotExists()
    {
        $this->setExpectedException('InvalidArgumentException', 'Property \'foo\' not present in model \'GeometriaLabTest\Model\TestModels\Model\'');
        $s = new Schema('GeometriaLabTest\Model\TestModels\Model');
        $this->assertNull($s->getProperty('foo'));
    }

    public function testParseDocblock()
    {
        $this->setExpectedException('InvalidArgumentException');
        new Schema('stdClass', 'Docblock not present');
    }

    public function testParsePropertyTag()
    {
        $this->setExpectedException('InvalidArgumentException', 'Property with name \'foo\' already exists');
        new Schema('GeometriaLabTest\Model\TestModels\WithInvalidSchema\DoubleProperty');
    }

    public function testGetParamsFromTag()
    {
        $this->setExpectedException('InvalidArgumentException', 'Not valid params for property \'foo\'');
        new Schema('GeometriaLabTest\Model\TestModels\WithInvalidSchema\InvalidParams');
    }

    public function testCreateProperty()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid property type \'bulean\'');
        new Schema('GeometriaLabTest\Model\TestModels\WithInvalidSchema\InvalidType');
    }
}

