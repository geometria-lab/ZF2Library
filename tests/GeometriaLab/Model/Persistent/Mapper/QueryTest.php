<?php

namespace GeometriaLabTest\Model\Persistent\Mapper;

use GeometriaLabTest\Model\Persistent\TestModels\Model;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeometriaLab\Model\Persistent\Mapper\Query
     */
    protected $query;

    public function setUp()
    {
        $this->query = Model::getMapper()->createQuery();
    }

    public function testQueryInstance()
    {
        $this->assertInstanceOf('\GeometriaLab\Model\Persistent\Mapper\Query', $this->query);
    }

    public function testGetMapper()
    {
        $this->assertEquals($this->query->getMapper(), Model::getMapper());
    }

    public function testSelect()
    {
        $this->query->select(array('floatProperty', 'integerProperty'));

        $this->assertEquals(array('floatProperty', 'integerProperty'), $this->query->getSelect());
    }

    public function testSelectUndefinedFields()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $this->query->select(array('floatProperty', 'undefinedProperty'));
    }

    public function testHasSelect()
    {
        $this->assertFalse($this->query->hasSelect());

        $this->query->select(array('floatProperty', 'integerProperty'));

        $this->assertTrue($this->query->hasSelect());
    }

    public function testResetSelect()
    {
        $this->query->select(array('floatProperty', 'integerProperty'));

        $this->query->resetSelect();

        $this->assertFalse($this->query->hasSelect());
    }

    public function testWhere()
    {
        $where = array('floatProperty' => 0.1, 'integerProperty' => 1);

        $this->query->where($where);

        $this->assertEquals($where, $this->query->getWhere());

        $this->query->where(array('id' => 4));

        $where['id'] = 4;

        $this->assertEquals($where, $this->query->getWhere());
    }

    public function testWhereWithUndefinedFields()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $this->query->where(array('floatProperty' => 0.1, 'undefinedProperty' => 1));
    }

    public function testWhereWithInvalidValue()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $this->query->where(array('floatProperty' => 1));
    }

    public function testHasWhere()
    {
        $this->assertFalse($this->query->hasWhere());

        $this->query->where(array('floatProperty' => 1.1));

        $this->assertTrue($this->query->hasWhere());
    }

    public function testResetWhere()
    {
        $this->query->where(array('floatProperty' => 1.1));

        $this->query->resetWhere();

        $this->assertFalse($this->query->hasWhere());
    }

    public function testSort()
    {
        $this->query->sort('floatProperty');
        $this->query->sort('integerProperty', false);

        $result = array('floatProperty' => true, 'integerProperty' => false);

        $this->assertEquals($result, $this->query->getSort());
    }

    public function testSortWithUndefinedFields()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->query->sort('undefinedProperty');
    }

    public function testHasSort()
    {
        $this->assertFalse($this->query->hasSort());
        $this->query->sort('floatProperty');
        $this->assertTrue($this->query->hasSort());
    }

    public function testResetSort()
    {
        $this->query->sort('floatProperty');
        $this->assertTrue($this->query->hasSort());
        $this->query->resetSort();
        $this->assertFalse($this->query->hasSort());
    }

    public function testLimit()
    {
        $this->query->limit(1);
        $this->assertEquals(1, $this->query->getLimit());
    }

    public function testHasLimit()
    {
        $this->assertFalse($this->query->hasLimit());
        $this->query->limit(1);
        $this->assertTrue($this->query->hasLimit());
    }

    public function testResetLimit()
    {
        $this->query->limit(1);
        $this->assertTrue($this->query->hasLimit());
        $this->query->resetLimit();
        $this->assertFalse($this->query->hasLimit());
    }

    public function testOffset()
    {
        $this->query->offset(1);
        $this->assertEquals(1, $this->query->getOffset());
    }

    public function testHasOffset()
    {
        $this->assertFalse($this->query->hasOffset());
        $this->query->offset(1);
        $this->assertTrue($this->query->hasOffset());
    }

    public function testResetOffset()
    {
        $this->query->offset(1);
        $this->assertTrue($this->query->hasOffset());
        $this->query->resetOffset();
        $this->assertFalse($this->query->hasOffset());
    }
}
