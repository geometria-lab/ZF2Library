<?php

namespace GeometriaLabTest\Stdlib\Extractor;

use GeometriaLab\Model\Collection,
    GeometriaLab\Api\Paginator\ModelPaginator,
    GeometriaLab\Api\Stdlib\Extractor\Service,
    GeometriaLabTest\Stdlib\Extractor\TestExtractors\User as UserExtractor,
    GeometriaLabTest\Stdlib\Extractor\TestExtractors\Order as OrderExtractor,
    GeometriaLabTest\Stdlib\Extractor\TestModels\User,
    GeometriaLabTest\Stdlib\Extractor\TestModels\Order;

class ExtractorTest extends \PHPUnit_Framework_TestCase
{
    static private $order;
    static private $user;

    public function setUp()
    {
        self::$order = new Order();
        self::$order->id = 2;
        self::$order->transactionId = 123;

        self::$user = new User();
        self::$user->id = 1;
        self::$user->name = 'Bender';
        self::$user->order = self::$order;
    }

    public function testExtractModel()
    {
        $extractor = new OrderExtractor();
        $data = $extractor->extract(self::$order);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testExtractCollection()
    {
        $collection = new Collection();
        $collection->push(self::$order);

        $extractor = new OrderExtractor();
        $data = $extractor->extract($collection);

        $this->assertEquals($data, array('id' => 2, 'transactionId' => 123));
    }

    public function testFilters()
    {
        $extractor = new UserExtractor();
        $data = $extractor->extract(self::$user);

        $this->assertTrue(isset($data['name']));

        $this->assertEquals($data['name'], self::$user->name . ' Rodriguez');
    }

    public function testExtractRecursive()
    {
        $extractorService = new Service();
        $data = $extractorService->setNamespace('GeometriaLabTest\Stdlib\Extractor\TestExtractors')->extract(self::$user);

        $this->assertTrue(isset($data['order']));

        $this->assertEquals($data['order'], array('id' => 2, 'transactionId' => 123));
    }
}
