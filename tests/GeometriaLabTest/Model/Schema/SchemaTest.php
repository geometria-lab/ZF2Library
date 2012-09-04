<?php

namespace GeometriaLabTest\Model\Schema;

use GeometriaLab\Model\Schema\DocBlockParser;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPropertyNotExists()
    {
        $this->setExpectedException('InvalidArgumentException', 'Property \'foo\' not present in model \'GeometriaLabTest\Model\TestModels\Model\'');
        $s = DocBlockParser::getInstance()->getSchema('GeometriaLabTest\Model\TestModels\Model');
        $this->assertNull($s->getProperty('foo'));
    }

    public function testParseDocblock()
    {
        $this->setExpectedException('InvalidArgumentException');
        DocBlockParser::getInstance()->getSchema('stdClass');
    }

    public function testGetParamsFromTag()
    {
        $this->setExpectedException('InvalidArgumentException', 'Not valid params for property \'foo\'');
        DocBlockParser::getInstance()->getSchema('GeometriaLabTest\Model\Schema\TestModels\ModelWithInvalidParams');
    }

    public function testCreateProperty()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid property type \'bulean\'');
        DocBlockParser::getInstance()->getSchema('GeometriaLabTest\Model\Schema\TestModels\ModelWithInvalidType');
    }
}

