<?php

namespace GeometriaLabTest\Stdlib\Extractor;

use GeometriaLabTest\Stdlib\Extractor\TestExtractors\User,
    GeometriaLabTest\Stdlib\Extractor\TestExtractors\Order;

class ExtractorTest extends \PHPUnit_Framework_TestCase
{
    private $order;
    private $user;

    public function setUp()
    {
        $this->order = new \stdClass();
        $this->order->id = 2;
        $this->order->transactionId = 123;

        $this->user = new \stdClass();
        $this->user->id = 1;
        $this->user->name = 'Bender';
        $this->user->order = $this->order;
    }

    public function testExtract()
    {
        $hydrator = new Order();
        $data = $hydrator->extract($this->order);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testFilters()
    {
        $hydrator = new User();
        $data = $hydrator->extract($this->user);

        $this->assertTrue(isset($data['name']));

        $this->assertEquals($data['name'], $this->user->name . ' Rodriguez');
    }

    public function testExtractRecursive()
    {
        $hydrator = new User();
        $data = $hydrator->extract($this->user);

        $this->assertTrue(isset($data['order']));

        $this->assertEquals($data['order'], array('id' => 2, 'transactionId' => 123));
    }
}
