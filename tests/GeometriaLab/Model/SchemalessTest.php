<?php

namespace GeometriaLabTest\Model;

use GeometriaLab\Model\Schemaless;

class SchemalessTest extends \PHPUnit_Framework_TestCase
{
    public function testPopulateOnConstruct()
    {
        $m = new Schemaless(array('test' => true));
        $this->assertTrue($m->test);
    }

    public function testPopulateByInvalidData()
    {
        $this->setExpectedException('InvalidArgumentException');
        $m = new Schemaless();
        $m->populate(true);
    }

    public function testPopulateByArray()
    {
        $m = new Schemaless();
        $m->populate(array('test' => true));
        $this->assertTrue($m->get('test'));
    }

    public function testPopulateByObject()
    {
        $o = new \stdClass();
        $o->test = true;

        $m = new Schemaless();
        $m->populate($o);
        $this->assertTrue($m->get('test'));
    }

    public function testGetNotPresentProperty()
    {
        $m = new Schemaless();
        $this->assertNull($m->get('test'));
    }

    public function testGet()
    {
        $m = new Schemaless();
        $m->populate(array(
            'test1' => 1,
            'test2' => 2
        ));

        $this->assertEquals(1, $m->get('test1'));
        $this->assertEquals(2, $m->get('test2'));
    }

    public function testMagicGet()
    {
        $m = new Schemaless();
        $m->populate(array(
            'test1' => 1,
            'test2' => 2
        ));

        $this->assertEquals(1, $m->test1);
        $this->assertEquals(2, $m->test2);
    }

    public function testSet()
    {
        $m = new Schemaless();
        $m->set('test1', 1)
          ->set('test2', 2);

        $this->assertEquals(1, $m->get('test1'));
        $this->assertEquals(2, $m->get('test2'));

    }

    public function testMagicSet()
    {
        $m = new Schemaless();
        $m->test1 = 1;
        $m->test2 = 2;

        $this->assertEquals(1, $m->get('test1'));
        $this->assertEquals(2, $m->get('test2'));
    }

    public function testHas()
    {
        $m = new Schemaless();
        $m->populate(array(
            'test1' => 1,
            'test2' => null
        ));

        $this->assertFalse($m->has('test2'));
        $this->assertFalse($m->has('test3'));
    }

    public function testMagicHas()
    {
        $m = new Schemaless();
        $m->populate(array(
            'test1' => 1,
            'test2' => null
        ));

        $this->assertFalse(isset($m->test2));
        $this->assertFalse(isset($m->test3));
    }

    public function testToArray()
    {
        $array = array(
            'test1' => 1,
            'test2' => 2
        );
        $m = new Schemaless();
        $m->populate($array);
        $this->assertEquals($array, $m->toArray());
    }

    public function testIterator()
    {
        $array = array(
            'test1' => 1,
            'test2' => 2,
            'test3' => 3,
            'test4' => 4,
            'test5' => 5
        );
        $m = new Schemaless();
        $m->populate($array);

        $this->_iterate($m, $array);
        $this->_iterate($m, $array);
    }

    protected function _iterate($m, $array)
    {
        reset($array);
        foreach($m as $key => $value) {
            $this->assertEquals(key($array), $key);
            $this->assertEquals(current($array), $value);
            next($array);
        }
    }

    public function testCount()
    {
        $array = array(
            'test1' => 1,
            'test2' => 2
        );
        $m = new Schemaless();
        $m->populate($array);
        $this->assertEquals(2, count($m));
    }
}

