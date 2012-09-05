<?php

namespace GeometriaLabTest\Stdlib;

class HydratorTest extends \PHPUnit_Framework_TestCase
{
    private $order;
    private $user;

    public function setUp()
    {
        $this->order = new \stdClass();
        $this->order->id = 2;

        $this->user = new \stdClass();
        $this->user->id = 1;
        $this->user->name = 'Bender';
        $this->user->order = $this->order;
    }

    public function testExtract()
    {
        $hydrator = new TestHydrators\Order();
        $data = $hydrator->extract($this->order);

        $this->assertEquals($data, array('id' => 2));
    }

    public function testFilters()
    {
        $hydrator = new TestHydrators\User();
        $data = $hydrator->extract($this->user);

        $this->assertTrue(isset($data['name']));

        $this->assertEquals($data['name'], $this->user->name . ' Rodriguez');
    }

    public function testExtractRecursive()
    {
        $hydrator = new TestHydrators\User();
        $data = $hydrator->extract($this->user);

        $this->assertTrue(isset($data['order']));

        $this->assertEquals($data['order'], array('id' => 2));
    }
}
