<?php

namespace GeometriaLabTest\Api\Stdlib\Extractor;

use GeometriaLab\Model\Collection,
    GeometriaLab\Api\Paginator\ModelPaginator,
    GeometriaLab\Api\Stdlib\Extractor\Service;

use GeometriaLabTest\Api\Stdlib\Extractor\TestModels\User,
    GeometriaLabTest\Api\Stdlib\Extractor\TestModels\Order,
    GeometriaLabTest\Api\Stdlib\Extractor\TestModels\PersistentModel;

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
        self::$user->about = 'I am a fictional robot character in the animated television series Futurama';
        self::$user->order = self::$order;

        self::$extractorService = new Service();
        self::$extractorService->setNamespace('GeometriaLabTest\Api\Stdlib\Extractor\TestExtractors');
    }

    public function testExtractModel()
    {
        $data = self::$extractorService->extract(self::$order);

        $expected = array(
            'item' => array(
                'id' => 2,
                'transactionId' => 123,
            ),
            'type' => 'Order',
        );

        $this->assertEquals($expected, $data);
    }

    public function testExtractCollection()
    {
        $collection = new Collection();
        $collection->push(self::$order);

        $data = self::$extractorService->extract($collection);

        $expected = array(
            'items' => array(
                array(
                    'id' => 2,
                    'transactionId' => 123,
                ),
            ),
            'type' => 'Order',
        );
        $this->assertEquals($expected, $data);
    }

    public function testExtractModelPaginator()
    {
        $model = new PersistentModel(array('name' => 'one'));
        $model->save();

        $model = new PersistentModel(array('name' => 'two'));
        $model->save();

        $model = new PersistentModel(array('name' => 'three'));
        $model->save();

        $paginator = new ModelPaginator(PersistentModel::getMapper()->createQuery());
        $paginator->setLimit(1);

        $data = self::$extractorService->extract($paginator);

        $expected = array(
            'items' => array(
                array(
                    'id'    => 1,
                    'name'  => 'one',
                ),
            ),
            'totalCount'    => 3,
            'limit'         => 1,
            'offset'        => null,
            'type'          => 'PersistentModel',
        );

        $this->assertEquals($expected, $data);
    }

    public function testExtractEmptyModelPaginator()
    {
        PersistentModel::getMapper()->deleteByQuery(PersistentModel::getMapper()->createQuery());

        $paginator = new ModelPaginator(PersistentModel::getMapper()->createQuery());
        $paginator->setLimit(1);

        $data = self::$extractorService->extract($paginator);

        $expected = array(
            'items' => array(
            ),
            'totalCount'    => 0,
            'limit'         => 1,
            'offset'        => null,
        );

        $this->assertEquals($expected, $data);
    }

    public function testCallableFilters()
    {
        $data = self::$extractorService->extract(self::$user);

        $this->assertTrue(isset($data['item']['name']));

        $this->assertEquals($data['item']['name'], self::$user->name . ' Rodriguez');
    }

    public function testArrayFilters()
    {
        $data = self::$extractorService->extract(self::$user);

        $this->assertTrue(isset($data['item']['about']));
        $this->assertEquals($data['item']['about'], 'I am a fictional robot character in the animated television series ');
    }

    public function testExtractCallable()
    {
        $data = self::$extractorService->extract(self::$user);

        $this->assertTrue(isset($data['item']['callable']));

        $this->assertEquals('Foo', $data['item']['callable']);
    }

    public function testExtractRecursive()
    {
        $data = self::$extractorService->extract(self::$user);

        $this->assertTrue(isset($data['item']['order']));

        $expected = array(
            'item' => array(
                'id' => 2,
                'transactionId' => 123,
            ),
            'type' => 'Order',
        );

        $this->assertEquals($expected, $data['item']['order']);
    }

    public function testExtractAllFields()
    {
        $fields = array('*' => true);
        $data = self::$extractorService->extract(self::$order, $fields);

        $expected = array(
            'item' => array(
                'id' => 2,
                'transactionId' => 123,
            ),
            'type' => 'Order',
        );

        $this->assertEquals($expected, $data);
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

        $expected = array(
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

        $this->assertEquals($expected, $data);
    }

    public function testExtractAllNestedFields()
    {
        $fields = array('*' => true);
        $data = self::$extractorService->extract(self::$user, $fields);

        $expected = array(
            'item' => array(
                'id' => 1,
                'name' => 'Bender Rodriguez',
                'about' => 'I am a fictional robot character in the animated television series ',
                'order' => array(
                    'item' => array(
                        'id' => 2,
                        'transactionId' => 123,
                    ),
                    'type' => 'Order',
                ),
                'callable' => 'Foo',
            ),
            'type' => 'User',
        );

        $this->assertEquals($expected, $data);
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
