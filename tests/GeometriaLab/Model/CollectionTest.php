<?php

namespace GeometriaLabTest\Model;

use GeometriaLab\Model\Collection,
    GeometriaLab\Model\Schemaless;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $models;

    public function setUp()
    {
        $this->models = array(
            new Schemaless(array('test' => 1)),
            new Schemaless(array('test' => 2)),
            new Schemaless(array('test' => 3)),
            new Schemaless(array('test' => 4)),
            new Schemaless(array('test' => 5))
        );
    }

    public function testPopulateOnConstruct()
    {
        $c = new Collection($this->models);
        $this->assertEquals($this->models, $c->toArray());

        $c = new Collection($this->models[0]);
        $this->assertEquals(array($this->models[0]), $c->toArray());
    }

    public function testPush()
    {
        $c = new Collection();
        $c->push($this->models);
        $this->assertEquals($this->models, $c->toArray());

        $c = new Collection();
        $c->push($this->models[0]);
        $this->assertEquals(array($this->models[0]), $c->toArray());
    }

    public function testPushWithInvalidArgument()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $c = new Collection();
        $c->push(1);
    }

    public function testPop()
    {
        $c = new Collection($this->models);

        $this->assertEquals($this->models[4], $c->pop());
        $this->assertEquals($this->models[3], $c->pop());
    }

    public function testUnshift()
    {
        $c = new Collection();
        $c->unshift($this->models);
        $this->assertEquals(array_reverse($this->models), $c->toArray());

        $c = new Collection();
        $c->unshift($this->models[0]);
        $this->assertEquals(array($this->models[0]), $c->toArray());
    }

    public function testUnshiftWithInvalidArgument()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $c = new Collection();
        $c->unshift(1);
    }

    public function testShift()
    {
        $c = new Collection($this->models);
        $this->assertEquals($this->models[0], $c->shift());
        $this->assertEquals($this->models[1], $c->shift());
    }

    public function testSet()
    {
        $c = new Collection($this->models);

        $model = new Schemaless(array('test' => 'new'));
        $c->set(1, $model);

        $models = $c->toArray();
        $this->assertEquals($model, $models[1]);
    }

    public function testRemove()
    {
        $c = new Collection($this->models);
        $c->remove($this->models[1]);
        $models = $c->toArray();
        $this->assertEquals($this->models[2], $models[1]);
    }

    public function testGet()
    {
        $c = new Collection($this->models);
        $this->assertEquals($this->models[0], $c->get(0));
        $this->assertNull($c->get(10));
    }

    public function testGetFirst()
    {
        $c = new Collection($this->models);
        $this->assertEquals($this->models[0], $c->getFirst());
    }

    public function testGetLast()
    {
        $c = new Collection($this->models);
        $this->assertEquals(end($this->models), $c->getLast());
    }

    public function testShuffle()
    {
        $c = new Collection($this->models);
        $c->shuffle();
        $this->assertNotEquals($this->models, $c->toArray());
    }

    public function testReverse()
    {
        $c = new Collection($this->models);
        $c->reverse();
        $this->assertEquals(array_reverse($this->models), $c->toArray());
    }

    public function testClear()
    {
        $c = new Collection($this->models);
        $c->clear();

        $this->assertEquals(array(), $c->toArray());
    }

    public function testIsEmpty()
    {
        $c = new Collection();
        $this->assertTrue($c->isEmpty());

        $c->push($this->models);
        $this->assertFalse($c->isEmpty());
    }

    public function testGetByCondition()
    {
        $c = new Collection($this->models);
        $c2 = $c->getByCondition(array('test' => 1));

        $this->assertEquals(array($this->models[0]), $c2->toArray());

        $c3 = $c->getByCondition(function($model) {
            return $model->test === 2;
        });

        $this->assertEquals(array($this->models[1]), $c3->toArray());
    }

    public function testGetSlice()
    {
        $c = new Collection($this->models);
        $c2 = $c->getSlice(2, 2);

        $this->assertEquals(array($this->models[2], $this->models[3]), $c2->toArray());
    }

    public function testSort()
    {
        $sortCallback = function($model1, $model2) {
            if ($model1->test > $model2->test) {
                return -1;
            } else {
                return 1;
            }
        };

        $c = new Collection($this->models);
        $c->sort($sortCallback);

        usort($this->models, $sortCallback);
        $this->assertEquals($this->models, $c->toArray());
    }

    public function testIterator()
    {
        $c = new Collection($this->models);

        $this->_iterate($c);
        $this->_iterate($c);
    }

    protected function _iterate($c)
    {
        reset($this->models);
        foreach($c as $key => $value) {
            $this->assertEquals(key($this->models), $key);
            $this->assertEquals(current($this->models), $value);
            next($this->models);
        }
    }

    public function testCount()
    {
        $c = new Collection($this->models);
        $this->assertEquals(5, count($c));
    }

    public function testOffsetExists()
    {
        $c = new Collection($this->models);
        $this->assertTrue(isset($c[0]));
        $this->assertFalse(isset($c[10]));
    }

    public function testOffsetGet()
    {
        $c = new Collection($this->models);
        $this->assertEquals($this->models[0], $c[0]);
        $this->assertNull($c[10]);
    }

    public function testOffsetSet()
    {
        $c = new Collection($this->models);
        $c[0] = $this->models[4];
        $this->assertEquals($this->models[4], $c->get(0));
    }

    public function testOffsetUnset()
    {
        $c = new Collection($this->models);
        unset($c[4]);
        $this->assertEquals(4, count($c));
    }
}