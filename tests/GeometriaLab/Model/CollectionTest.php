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

        $this->assertEquals($this->models[0], $c->pop());
        $this->assertEquals($this->models[1], $c->pop());
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
        $this->assertEquals($this->models[1], $c->shift());
        $this->assertEquals($this->models[0], $c->shift());
    }

    public function testSet()
    {
        $c = new Collection($this->models);

        $model = new Schemaless(array('test' => 'new'));
        $c->set(1, $model);

        $this->assertEquals($this->models, $c->toArray()[1]);
    }

    public function testRemove()
    {
        $c = new Collection($this->models);
        $c->remove($this->models[1]);
        $this->assertEquals($this->models[2], $c->toArray()[1]);
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
        $this->markTestIncomplete();
    }

    public function testSort()
    {
        $this->markTestIncomplete();

        //usort($this->models, $callback);
        //$this->rewind();
    }

    public function testIterator()
    {
        $this->markTestIncomplete();
    }

    public function testCount()
    {
        $this->markTestIncomplete();
    }

    public function testOffsetExists()
    {
        $this->markTestIncomplete();
    }

    public function testOffsetGet()
    {
        $this->markTestIncomplete();
    }

    public function testOffsetSet()
    {
        $this->markTestIncomplete();
    }

    public function testOffsetUnset()
    {
        $this->markTestIncomplete();
    }
}