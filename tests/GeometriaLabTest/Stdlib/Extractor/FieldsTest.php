<?php

namespace GeometriaLabTest\Stdlib\Extractor;

use GeometriaLab\Api\Stdlib\Extractor\Service;

use GeometriaLabTest\Stdlib\Extractor\TestExtractors\User,
    GeometriaLabTest\Stdlib\Extractor\TestExtractors\Order;

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
        $fields = Service::createFieldsFromString('id,transactionId');

        $this->assertTrue(isset($fields['id']));

        $this->assertTrue(isset($fields['transactionId']));
    }

    public function testExtractOneField()
    {
        $fields = Service::createFieldsFromString('id');
        $extractor = new Order();
        $data = $extractor->extract($this->order, $fields);

        $this->assertEquals($data, array('id' => 2));
    }

    public function testExtractSeveralFields()
    {
        $fields = Service::createFieldsFromString('id,transactionId');
        $extractor = new Order();
        $data = $extractor->extract($this->order, $fields);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testExtractWrongField()
    {
        $this->setExpectedException('\GeometriaLab\Api\Exception\WrongFields');

        $fields = Service::createFieldsFromString('id,foo');
        $extractor = new Order();
        $extractor->extract($this->order, $fields);
    }

    public function testExtractAllFields()
    {
        $fields = Service::createFieldsFromString('*');
        $extractor = new Order();
        $data = $extractor->extract($this->order, $fields);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testExtractAllNestedFields()
    {
        $fields = Service::createFieldsFromString('*');
        $extractor = new User();
        $data = $extractor->extract($this->user, $fields);

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
        $fields = Service::createFieldsFromString('id,order(transactionId)');
        $extractor = new User();
        $data = $extractor->extract($this->user, $fields);

        $equalsData = array(
            'id' => 1,
            'order' =>  array(
                'transactionId' => 123
            ),
        );

        $this->assertEquals($data, $equalsData);
    }

    public function testExtractWrongNestedField()
    {
        $this->setExpectedException('\GeometriaLab\Api\Exception\WrongFields');

        $fields = Service::createFieldsFromString('id,order(foo)');
        $extractor = new User();
        $extractor->extract($this->user, $fields);
    }
}
