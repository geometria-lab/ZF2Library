<?php

namespace GeometriaLabTest\Model\Schemaless;

use GeometriaLab\Model\Schemaless\Model;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testPopulateOnConstruct()
    {
        $m = new Model(array('test' => true));
        $this->assertTrue($m->test);
    }

    public function testPopulateByInvalidData()
    {
        $this->setExpectedException('InvalidArgumentException');
        $m = new Model();
        $m->populate(true);
    }

    public function testPopulateByArray()
    {
        $m = new Model();
        $m->populate(array('test' => true));
        $this->assertTrue($m->get('test'));
    }

    public function testPopulateByObject()
    {
        $o = new \stdClass();
        $o->test = true;

        $m = new Model();
        $m->populate($o);
        $this->assertTrue($m->get('test'));
    }

    public function testGetNotPresentProperty()
    {
        $m = new Model();
        $this->assertNull($m->get('test'));
    }

    public function testGet()
    {
        $m = new Model();
        $m->populate(array(
            'test1' => 1,
            'test2' => 2
        ));

        $this->assertEquals(1, $m->get('test1'));
        $this->assertEquals(2, $m->get('test2'));
    }

    public function testMagicGet()
    {
        $m = new Model();
        $m->populate(array(
            'test1' => 1,
            'test2' => 2
        ));

        $this->assertEquals(1, $m->test1);
        $this->assertEquals(2, $m->test2);
    }

    public function testSet()
    {
        $m = new Model();
        $m->set('test1', 1)
          ->set('test2', 2);

        $this->assertEquals(1, $m->get('test1'));
        $this->assertEquals(2, $m->get('test2'));

    }

    public function testMagicSet()
    {
        $m = new Model();
        $m->test1 = 1;
        $m->test2 = 2;

        $this->assertEquals(1, $m->get('test1'));
        $this->assertEquals(2, $m->get('test2'));
    }

    public function testHas()
    {
        $m = new Model();
        $m->populate(array(
            'test1' => 1
        ));

        $this->assertTrue($m->has('test1'));
        $this->assertFalse($m->has('test2'));
        $this->assertFalse($m->has('test3'));
    }

    public function testMagicHas()
    {
        $m = new Model();
        $m->populate(array(
            'test1' => 1
        ));

        $this->assertTrue(isset($m->test1));
        $this->assertFalse(isset($m->test2));
        $this->assertFalse(isset($m->test3));
    }

    public function testToArray()
    {
        $array = array(
            'test1' => 1,
            'test2' => 2,
            'test3' => new Model(array('test1' => 1))
        );
        $m = new Model();
        $m->populate($array);
        $this->assertEquals($array, $m->toArray());
    }

    public function testToArrayWithDepth()
    {
        $array = array(
            'test1' => 1,
            'test2' => 2,
            'test3' => new Model(array('test1' => 1))
        );

        $m = new Model();
        $m->populate($array);

        $resultArray = array(
            'test1' => 1,
            'test2' => 2,
            'test3' => array('test1' => 1)
        );

        $this->assertEquals($resultArray, $m->toArray(-1));
    }
}

