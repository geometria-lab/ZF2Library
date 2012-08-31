<?php

namespace GeometriaLab\Test;

use GeometriaLab\Mongo\ServiceFactory as MongoServiceFactory,
    GeometriaLab\Mongo\Model\Mapper as MongoMapper;

use Zend\Config\Config as ZendConfig,
    Zend\ServiceManager\ServiceManager as ZendServiceManager,
    Zend\Mvc\Service\ServiceManagerConfig as ZendServiceManagerConfig;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    static protected $config;
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
        $config = self::$config;
        self::$sm = new ZendServiceManager(
            new ZendServiceManagerConfig(array(
                'factories' => array(
                    'MongoManager' => '\GeometriaLab\Mongo\ServiceFactory',
                    'Configuration' => function($sm) use ($config) {
                        return new ZendConfig(
                            $config
                        );
                    },
                ),
            ))
        );
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
     * @param array $config
     */
    final static public function setConfig($config = array())
    {
        self::$config = $config;
    }
}
