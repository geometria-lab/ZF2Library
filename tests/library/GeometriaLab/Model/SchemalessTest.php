<?php

class GeometriaLab_Model_SchemalessTest extends PHPUnit_Framework_TestCase
{
    public function testPopulateOnConstruct()
    {
        $m = new GeometriaLab_Model_Schemaless(array('test' => true));
        $this->assertTrue($m->test);
    }

    public function testPopulateByInvalidData()
    {
        $this->setExpectedException('GeometriaLab_Model_Exception');
        $m = new GeometriaLab_Model_Schemaless();
        $m->populate(true);
    }

    public function testPopulateByArray()
    {
        $m = new GeometriaLab_Model_Schemaless();
        $m->populate(array('test' => true));
        $this->assertTrue($m->get('test'));
    }

    public function testPopulateByObject()
    {
        $o = new stdClass();
        $o->test = true;

        $m = new GeometriaLab_Model_Schemaless();
        $m->populate($o);
        $this->assertTrue($m->get('test'));
    }

    public function testGetNotPresentProperty()
    {
        $m = new GeometriaLab_Model_Schemaless();
        $this->assertNull($m->get('test'));
    }

    public function testGet()
    {
        $m = new GeometriaLab_Model_Schemaless();
        $m->populate(array(
            'test1' => 1,
            'test2' => 2
        ));

        $this->assertEquals(1, $m->get('test1'));
        $this->assertEquals(2, $m->get('test2'));
    }

    public function testMagicGet()
    {
        $m = new GeometriaLab_Model_Schemaless();
        $m->populate(array(
            'test1' => 1,
            'test2' => 2
        ));

        $this->assertEquals(1, $m->test1);
        $this->assertEquals(2, $m->test1);
    }

    public function testSet()
    {
        $m = new GeometriaLab_Model_Schemaless();
        $m->set('test1', 1)
          ->set('test2', 2);

        $this->assertEquals(1, $m->get('test1'));
        $this->assertEquals(2, $m->get('test2'));

    }

    public function testMagicSet()
    {
        $m = new GeometriaLab_Model_Schemaless();
        $m->test1 = 1;
        $m->test2 = 2;

        $this->assertEquals(1, $m->get('test1'));
        $this->assertEquals(2, $m->get('test2'));
    }

    public function testHas()
    {
        $m = new GeometriaLab_Model_Schemaless();
        $m->populate(array(
            'test1' => 1,
            'test2' => null
        ));

        $this->assertFalse($m->has('test2'));
        $this->assertFalse($m->has('test3'));
    }

    public function testMagicHas()
    {
        $m = new GeometriaLab_Model_Schemaless();
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
        $m = new GeometriaLab_Model_Schemaless();
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
        $m = new GeometriaLab_Model_Schemaless();
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
        $m = new GeometriaLab_Model_Schemaless();
        $m->populate($array);
        $this->assertEquals(2, count($m));
    }

    public function testInit()
    {
        class ModelWithInit extends GeometriaLab_Model_Schemaless
        {
            public function init()
            {
                $this->test = true;
            }
        }

        $m = new ModelWithInit();
        $this->assertTrue($m->test);
    }
}

