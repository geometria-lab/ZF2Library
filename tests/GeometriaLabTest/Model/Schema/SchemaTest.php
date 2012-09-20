<?php

namespace GeometriaLabTest\Model\Schema;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testAddProperty()
    {
        $modelSchema = new \GeometriaLab\Model\Schema\Schema();
        $persistentModelProperty = new \GeometriaLab\Model\Schema\Property\IntegerProperty(array('name' => 'integerProperty'));
        $modelSchema->addProperty($persistentModelProperty);

        $this->assertTrue($modelSchema->hasProperty('integerProperty'));
    }

    public function testAddBadProperty()
    {
        $this->setExpectedException('RuntimeException', 'Property \'integerProperty\' must be in \'GeometriaLab\Model\Schema\Property\' namespaces, but GeometriaLab\Model\Persistent\Schema\Property is given');

        $modelSchema = new \GeometriaLab\Model\Schema\Schema();
        $persistentModelProperty = new \GeometriaLab\Model\Persistent\Schema\Property\IntegerProperty(array('name' => 'integerProperty'));
        $modelSchema->addProperty($persistentModelProperty);
    }
}

