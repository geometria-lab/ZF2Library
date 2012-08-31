<?php

namespace GeometriaLab\Test;

use GeometriaLab\Mongo\ServiceFactory as MongoServiceFactory,
    GeometriaLab\Mongo\Model\Mapper as MongoMapper;

use Zend\ServiceManager\ServiceManager as ZendServiceManager;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ZendServiceManager
     */
    static protected $sm;
    /**
     * @var ZendServiceManager
     */
    static protected $serviceManager;

    static public function setUpBeforeClass()
    {

        MongoMapper::setServiceManager(self::$sm);
    }

    public function tearDown()
    {
        foreach (self::$sm->get('MongoManager')->getAll() as $mongoDb) {
            $mongoDb->drop();
        }
    }

    /**
     * @static
     * @param ZendServiceManager $sm
     */
    final static public function setServiceManager(ZendServiceManager $sm)
    {
        self::$sm = $sm;
    }
}
