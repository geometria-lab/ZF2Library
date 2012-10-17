<?php

namespace GeometriaLabTest\Model;

use GeometriaLabTest\Model\TestModels\Model;

use GeometriaLab\Model\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $models;

    public function setUp()
    {
        $this->models = array(
            new Model(array('test' => 1)),
            new Model(array('test' => 2)),
            new Model(array('test' => 3)),
            new Model(array('test' => 5)),
            new Model(array('test' => 4)),
            new Model(array('test' => 4))
        );
    }

    public function testEmptyCollection()
    {
        $c = new Collection();
        $this->assertNull($c->current());
        $this->assertNull($c->getFirst());
        $this->assertNull($c->getLast());
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
        $c->push(array(1));
    }

    public function testPushWithNotIteratedArgument()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $c = new Collection();
        $c->push(1);
    }

    public function testPop()
    {
        $c = new Collection($this->models);

        $this->assertEquals($this->models[5], $c->pop());
        $this->assertEquals($this->models[4], $c->pop());
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
        $c->unshift(array(1));
    }

    public function testUnshiftWithNotIteratedArgument()
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

        $model = new Model(array('test' => 'new'));
        $c->set(1, $model);

        $models = $c->toArray();
        $this->assertEquals($model, $models[1]);
    }

    public function testGetProperty()
    {
        $models = array(
            new Model(array('test' => 1)),
            new Model(array('test' => 2)),
        );
        $c = new Collection($models);
        $this->assertEquals(
            array(
                $this->models[0]->test,
                $this->models[1]->test,
            ),
            $c->getProperty('test')
        );
        $this->assertEquals(count($models), count($c->getProperty('test')));
        $this->assertEquals(array(null,null), $c->getProperty('test2'));
    }

    public function testGetPropertyPairs()
    {
        $model = array(
            new Model(array('test' => 'new', 'test2' => 'new2')),
        );
        $c = new Collection($model);
        $this->assertEquals(
            array($model[0]->test => $model[0]->test2),
            $c->getPropertyPairs('test', 'test2')
        );
    }

    public function testRemove()
    {
        $c = new Collection($this->models);
        $c->remove($this->models[1]);
        $models = $c->toArray();
        $this->assertEquals($this->models[2], $models[1]);
    }

    public function testRemoveByCondition()
    {
        $c = new Collection($this->models);
        $c->removeByCondition(array('test' => $this->models[0]->test));
        $models = $c->toArray();
        $this->assertEquals(count($this->models)-1, count($models));
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
        $models = array();
        for ($i=0; $i < 1000; $i++) {
            $models[] = new Model(array('test' => $i));
        }
        $c = new Collection($models);
        $c->shuffle();
        $this->assertNotEquals($models, $c->toArray());
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
    }

    public function testGetByCallback()
    {
        $c = new Collection($this->models);

        $c2 = $c->getByCallback(function($model) {
            return $model->test === 2;
        });

        $this->assertEquals(array($this->models[1]), $c2->toArray());
    }

    public function testGetSlice()
    {
        $c = new Collection($this->models);
        $c2 = $c->getSlice(2, 2);

        $this->assertEquals(array($this->models[2], $this->models[3]), $c2->toArray());
    }

    public function testSortByCallback()
    {
        $sortCallback = function($model1, $model2) {
            if ($model1->test > $model2->test) {
                return -1;
            } else {
                return 1;
            }
        };

        $c = new Collection($this->models);
        $c->sortByCallback($sortCallback);

        usort($this->models, $sortCallback);
        $this->assertEquals($this->models, $c->toArray());
    }

    public function testSort()
    {
        $c = new Collection($this->models);
        $c->sort(array('test' => false));

        $sortCallback = function($model1, $model2) {
            if ($model1->test > $model2->test) {
                return -1;
            } else {
                return 1;
            }
        };
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
        $count = 0;
        foreach($c as $key => $value) {
            $this->assertEquals(key($this->models), $key);
            $this->assertEquals(current($this->models), $value);
            next($this->models);
            $count++;
        }
        $this->assertEquals(count($this->models), $count);
    }

    public function testCount()
    {
        $c = new Collection($this->models);
        $this->assertEquals(count($this->models), count($c));
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

        $model =  new Model(array('test' => 6));
        $c[] = $model;

        $this->assertEquals($model, $c->get(6));
    }

    public function testOffsetUnset()
    {
        $c = new Collection($this->models);
        unset($c[4]);
        $this->assertEquals(count($this->models)-1, count($c));
    }

    public function testToArray()
    {
        $c = new Collection($this->models);
        $this->assertEquals($this->models, $c->toArray());
    }

    public function testToArrayWithDepth()
    {
        $c = new Collection($this->models);

        $result = array(
            array('test' => 1),
            array('test' => 2),
            array('test' => 3),
            array('test' => 5),
            array('test' => 4),
            array('test' => 4)
        );

        $this->assertEquals($result, $c->toArray(-1));
    }
}