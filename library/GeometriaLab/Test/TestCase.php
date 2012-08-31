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
    protected $sm;
    /**
     * @var ZendServiceManager
     */
    static protected $serviceManager;

    public function setUp()
    {
        $config = self::$config;
        $this->sm = new ZendServiceManager(
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
        MongoMapper::setServiceManager($this->sm);
    }

    public function tearDown()
    {
        foreach ($this->sm->get('MongoManager')->getAll() as $mongoDb) {
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
