<?php

namespace GeometriaLabTest\Model\Persistent;

use GeometriaLabTest\Model\Persistent\Models\PersistentModel;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GeometriaLab\Model\Persistent\Mapper\Query
     */
    protected $query;

    public function setUp()
    {
        $this->query = PersistentModel::getMapper()->createQuery();
    }

    public function testQueryInstance()
    {
        $this->assertInstanceOf('\GeometriaLab\Model\Persistent\Mapper\Query', $this->query);
    }

    public function testGetMapper()
    {
        $this->assertEquals($this->query->getMapper(), PersistentModel::getMapper());
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

    public function resetSelect()
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
        $this->markTestIncomplete();
    }

    public function testHasSort()
    {
        $this->markTestIncomplete();
    }

    public function testResetSort()
    {
        $this->markTestIncomplete();
    }

    public function testLimit()
    {
        $this->markTestIncomplete();
    }

    public function testHasLimit()
    {
        $this->markTestIncomplete();
    }

    public function testResetLimit()
    {
        $this->markTestIncomplete();
    }

    public function testOffset()
    {
        $this->markTestIncomplete();
    }

    public function testHasOffset()
    {
        $this->markTestIncomplete();
    }

    public function testResetOffset()
    {
        $this->markTestIncomplete();
    }
}
