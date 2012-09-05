<?php

namespace GeometriaLabTest\Stdlib;

use GeometriaLab\Api\Mvc\Controller\Action\Fields;

class FieldsTest extends \PHPUnit_Framework_TestCase
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

    public function testCreateFromString()
    {
        $fields = Fields::createFromString('id,transactionId');

        $this->assertTrue(isset($fields['id']));

        $this->assertTrue(isset($fields['transactionId']));
    }

    public function testExtractOneField()
    {
        $fields = Fields::createFromString('id');
        $hydrator = new TestHydrators\Order();
        $data = $hydrator->extract($this->order, $fields);

        $this->assertEquals($data, array('id' => 2));
    }

    public function testExtractSeveralFields()
    {
        $fields = Fields::createFromString('id,transactionId');
        $hydrator = new TestHydrators\Order();
        $data = $hydrator->extract($this->order, $fields);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testExtractAllFields()
    {
        $fields = Fields::createFromString('*');
        $hydrator = new TestHydrators\Order();
        $data = $hydrator->extract($this->order, $fields);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testExtractAllNestedFields()
    {
        $fields = Fields::createFromString('*');
        $hydrator = new TestHydrators\User();
        $data = $hydrator->extract($this->user, $fields);

        $equalsData = array(
            'id' => 1,
            'name' => 'Bender Rodriguez',
            'order' => array(
                'id' => 2,
                'transactionId' => 123
            ),
        );

        $this->assertEquals($data, $equalsData);
    }

    public function testExtractOneFieldsAndOneNestedFields()
    {
        $fields = Fields::createFromString('id,order(transactionId)');
        $hydrator = new TestHydrators\User();
        $data = $hydrator->extract($this->user, $fields);

        $equalsData = array(
            'id' => 1,
            'order' =>  array(
                'transactionId' => 123
            ),
        );

        $this->assertEquals($data, $equalsData);
    }
}
