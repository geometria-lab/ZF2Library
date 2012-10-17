<?php

namespace GeometriaLabTest\Api\Stdlib\Extractor;

use GeometriaLab\Model\Collection,
    GeometriaLab\Api\Stdlib\Extractor\Service,
    GeometriaLabTest\Api\Stdlib\Extractor\TestModels\User,
    GeometriaLabTest\Api\Stdlib\Extractor\TestModels\Order;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Order
     */
    static private $order;
    /**
     * @var User
     */
    static private $user;
    /**
     * @var Service
     */
    static private $extractorService;

    static public function setUpBeforeClass()
    {
        self::$order = new Order();
        self::$order->id = 2;
        self::$order->transactionId = 123;

        self::$user = new User();
        self::$user->id = 1;
        self::$user->name = 'Bender';
        self::$user->order = self::$order;

        self::$extractorService = new Service();
        self::$extractorService->setNamespace('GeometriaLabTest\Api\Stdlib\Extractor\TestExtractors');
    }

    public function testExtractModel()
    {
        $data = self::$extractorService->extract(self::$order);

        $actual = array(
            'item' => array(
                'id' => 2,
                'transactionId' => 123,
            ),
            'type' => 'Order',
        );

        $this->assertEquals($data, $actual);
    }

    public function testExtractCollection()
    {
        $collection = new Collection();
        $collection->push(self::$order);

        $data = self::$extractorService->extract($collection);

        $actual = array(
            'items' => array(
                array(
                    'id' => 2,
                    'transactionId' => 123,
                ),
            ),
            'type' => 'Order',
        );
        $this->assertEquals($data, $actual);
    }

    public function testFilters()
    {
        $data = self::$extractorService->extract(self::$user);

        $this->assertTrue(isset($data['item']['name']));

        $this->assertEquals($data['item']['name'], self::$user->name . ' Rodriguez');
    }

    public function testExtractRecursive()
    {
        $data = self::$extractorService->extract(self::$user);

        $this->assertTrue(isset($data['item']['order']));

        $actual = array(
            'item' => array(
                'id' => 2,
                'transactionId' => 123,
            ),
            'type' => 'Order',
        );

        $this->assertEquals($data['item']['order'], $actual);
    }

    public function testExtractAllFields()
    {
        $fields = array('*' => true);
        $data = self::$extractorService->extract(self::$order, $fields);

        $actual = array(
            'item' => array(
                'id' => 2,
                'transactionId' => 123,
            ),
            'type' => 'Order',
        );

        $this->assertEquals($data, $actual);
    }

    public function testExtractOneFieldsAndOneNestedFields()
    {
        $fields = array(
            'id' => true,
            'order' => array(
                'transactionId' => true,
            ),
        );
        $data = self::$extractorService->extract(self::$user, $fields);

        $actual = array(
            'item' => array(
                'id' => 1,
                'order' =>  array(
                    'item' => array(
                        'transactionId' => 123,
                    ),
                    'type' => 'Order',
                ),
            ),
            'type' => 'User',
        );

        $this->assertEquals($data, $actual);
    }

    public function testExtractAllNestedFields()
    {
        $fields = array('*' => true);
        $data = self::$extractorService->extract(self::$user, $fields);

        $actual = array(
            'item' => array(
                'id' => 1,
                'name' => 'Bender Rodriguez',
                'order' => array(
                    'item' => array(
                        'id' => 2,
                        'transactionId' => 123,
                    ),
                    'type' => 'Order',
                ),
            ),
            'type' => 'User',
        );

        $this->assertEquals($data, $actual);
    }

    public function testExtractWrongField()
    {
        $fields = array('id' => true, 'foo' => true);
        self::$extractorService->extract(self::$order, $fields);
        $invalidFields = self::$extractorService->getInvalidFields();

        $this->assertEquals(array('foo'), $invalidFields);
    }

    public function testExtractWrongNestedField()
    {
        $fields = array(
            'id' => true,
            'foo' => true,
            'order' => array(
                'foo' => true,
            ),
        );
        self::$extractorService->extract(self::$user, $fields);
        $invalidFields = self::$extractorService->getInvalidFields();

        $this->assertEquals(array('foo', 'order' => array('foo')), $invalidFields);
    }
}
