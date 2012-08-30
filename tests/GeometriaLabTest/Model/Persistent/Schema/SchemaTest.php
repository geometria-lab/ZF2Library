<?php

namespace GeometriaLabTest\Model\Persistent\Schema;

use GeometriaLab\Model\Persistent\Schema\Schema;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testParseDocblock()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Primary property (primary key) not present!');
        new Schema('GeometriaLabTest\Model\Persistent\Schema\TestModels\ModelWithOutPrimaryKey');
    }

    public function testParseMethodTag()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Mapper method tag in docblock must be static!');
        new Schema('GeometriaLabTest\Model\Persistent\Schema\TestModels\ModelWithMethodNonStatic');
    }

    public function testCreatePropertyInvalidType()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid property type \'bulean\'');
        new Schema('GeometriaLabTest\Model\Persistent\Schema\TestModels\ModelWithInvalidType');
    }
}

