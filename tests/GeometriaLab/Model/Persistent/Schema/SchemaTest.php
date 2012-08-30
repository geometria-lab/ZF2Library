<?php

namespace GeometriaLabTest\Model\Persistent\Schema;

use GeometriaLab\Model\Persistent\Schema\Schema,
    GeometriaLabTest\Model\Persistent\Schema\TestModels\WithOutPrimaryKey,
    GeometriaLabTest\Model\Persistent\Schema\TestModels\MethodNonStatic,
    GeometriaLabTest\Model\Persistent\Schema\TestModels\InvalidType;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testParseDocblock()
    {
        //$this->setExpectedException('\InvalidArgumentException', 'Primary property (primary key) not present!');
        new Schema('GeometriaLabTest\Model\Persistent\Schema\TestModels\WithOutPrimaryKey');
    }

    public function testParseMethodTag()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Mapper method tag in docblock must be static!');
        new Schema('GeometriaLabTest\Model\Persistent\Schema\TestModels\MethodNonStatic');
    }

    public function testCreatePropertyInvalidType()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid property type \'bulean\'');
        new Schema('GeometriaLabTest\Model\Persistent\Schema\TestModels\InvalidType');
    }
}

