<?php

namespace GeometriaLabTest\Model\Schema;

use GeometriaLab\Model\Schema\DocBlockParser;

class DocBlockParserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPropertyNotExists()
    {
        $this->setExpectedException('InvalidArgumentException', 'Property \'foo\' not present in model \'GeometriaLabTest\Model\TestModels\Model\'');
        $s = DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\TestModels\Model');
        $this->assertNull($s->getProperty('foo'));
    }

    public function testParseDocBlock()
    {
        $this->setExpectedException('InvalidArgumentException');
        DocBlockParser::getInstance()->createSchema('stdClass');
    }

    public function testParseDuplicatePropertyTag()
    {
        $this->setExpectedException('InvalidArgumentException', 'Property \'foo\' already exist in model \'GeometriaLabTest\Model\Schema\TestModels\ModelWithDoubleProperty\'');
        DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\Schema\TestModels\ModelWithDoubleProperty');
     }

    public function testGetParamsFromTag()
    {
        $this->setExpectedException('InvalidArgumentException', 'Not valid params for property \'foo\'');
        DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\Schema\TestModels\ModelWithInvalidParams');
    }

    public function testCreateProperty()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid property type \'bulean\'');
        DocBlockParser::getInstance()->createSchema('GeometriaLabTest\Model\Schema\TestModels\ModelWithInvalidType');
    }
}

