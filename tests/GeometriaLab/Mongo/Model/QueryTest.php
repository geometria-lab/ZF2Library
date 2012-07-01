<?php

namespace GeometriaLabTest\Mongo\Model;

use GeometriaLabTest\Mongo\Model\Models\Model;

use GeometriaLab\Mongo\Model\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp()
    {
        $this->query = Model::getMapper()->createQuery();
    }

    public function testSelect()
    {
        $fields = array('floatProperty' => true, 'integerProperty' => false);

        $this->query->select($fields);

        $this->assertEquals($fields, $this->query->getSelect());
    }

    public function testSelectUndefinedFields()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $this->query->select(array('floatProperty' => true, 'undefinedProperty' => false));
    }

    public function testWhere()
    {
        $where = array('floatProperty' => 0.1, 'integerProperty' => array('$in' => array(1, 2, 3)));

        $this->query->where($where);

        $this->assertEquals($where, $this->query->getWhere());

        $where['id'] = array('$in' => array("1dsad234","1dsad234","1dsad234"));

        $this->query->where(array('id' => $where['id']));

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

        $this->query->where(array('floatProperty' => 'string'));
    }

    public function testWhereWithInvalidValueInside()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $this->query->where(array('integerProperty' => array('$in' => array('string', 2, 3))));
    }
}
