<?php

namespace GeometriaLabTest\Stdlib\Extractor;

use GeometriaLab\Stdlib\Extractor\Fields;

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
        $fields = Fields::createFromString('id,transactionId');

        $this->assertTrue(isset($fields['id']));

        $this->assertTrue(isset($fields['transactionId']));
    }

    public function testExtractOneField()
    {
        $fields = Fields::createFromString('id');
        $extractor = new Order();
        $data = $extractor->extract($this->order, $fields);

        $this->assertEquals($data, array('id' => 2));
    }

    public function testExtractSeveralFields()
    {
        $fields = Fields::createFromString('id,transactionId');
        $extractor = new Order();
        $data = $extractor->extract($this->order, $fields);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testExtractWrongField()
    {
        $this->setExpectedException('\GeometriaLab\Api\Exception\WrongFields');

        $fields = Fields::createFromString('id,foo');
        $extractor = new Order();
        $extractor->extract($this->order, $fields);
    }

    public function testExtractAllFields()
    {
        $fields = Fields::createFromString('*');
        $extractor = new Order();
        $data = $extractor->extract($this->order, $fields);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testExtractAllNestedFields()
    {
        $fields = Fields::createFromString('*');
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
        $fields = Fields::createFromString('id,order(transactionId)');
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

        $fields = Fields::createFromString('id,order(foo)');
        $extractor = new User();
        $extractor->extract($this->user, $fields);
    }
}
