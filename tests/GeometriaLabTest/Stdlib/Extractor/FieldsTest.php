<?php

namespace GeometriaLabTest\Stdlib\Extractor;

use GeometriaLab\Api\Stdlib\Extractor\Service,
    GeometriaLabTest\Stdlib\Extractor\TestExtractors\User as UserExtractor,
    GeometriaLabTest\Stdlib\Extractor\TestExtractors\Order as OrderExtractor,
    GeometriaLabTest\Stdlib\Extractor\TestModels\User,
    GeometriaLabTest\Stdlib\Extractor\TestModels\Order;

class FieldsTest extends \PHPUnit_Framework_TestCase
{
    static private $order;
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
        self::$extractorService->setNamespace('GeometriaLabTest\Stdlib\Extractor\TestExtractors');
    }

    public function testExtractOneField()
    {
        $fields = array('id' => true);
        $data = self::$extractorService->extract(self::$order, $fields);

        $this->assertEquals($data, array('id' => 2));
    }

    public function testExtractSeveralFields()
    {
        $fields = array('id' => true, 'transactionId' => true);
        $data = self::$extractorService->extract(self::$order, $fields);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testExtractWrongField()
    {
        $fields = array('id' => true, 'foo' => true);
        self::$extractorService->extract(self::$order, $fields);
        $wrongFields = self::$extractorService->getWrongFields();

        $this->assertEquals(array('foo'), $wrongFields);
    }

    public function testExtractAllFields()
    {
        $fields = array('*' => true);
        $data = self::$extractorService->extract(self::$order, $fields);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testExtractAllNestedFields()
    {
        $fields = array('*' => true);
        $data = self::$extractorService->extract(self::$user, $fields);

        $equalsData = array(
            'id' => 1,
            'name' => 'Bender Rodriguez',
            'order' => array(
                'id' => 2,
                'transactionId' => 123,
            ),
        );

        $this->assertEquals($data, $equalsData);
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

        $equalsData = array(
            'id' => 1,
            'order' =>  array(
                'transactionId' => 123,
            ),
        );

        $this->assertEquals($data, $equalsData);
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
        $wrongFields = self::$extractorService->getWrongFields();

        $this->assertEquals(array('foo', 'order' => array('foo')), $wrongFields);
    }
}
