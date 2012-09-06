<?php

namespace GeometriaLabTest\Mongo;

use GeometriaLab\Model\Persistent\Mapper\Manager as MapperManager;

use GeometriaLab\Mongo\ServiceFactory as MongoServiceFactory,
    GeometriaLab\Mongo\Model\Mapper as MongoMapper;

use Zend\Config\Config as ZendConfig,
    Zend\ServiceManager\ServiceManager as ZendServiceManager,
    Zend\Mvc\Service\ServiceManagerConfig as ZendServiceManagerConfig;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ZendServiceManager
     */
    static protected $sm;
    /**
     * @var ZendServiceManager
     */
    static protected $serviceManager;

    public function tearDown()
    {
        $mapperManager = MapperManager::getInstance();

        foreach($mapperManager->getAll() as $mapper) {
            if ($mapper instanceof MongoMapper) {
                $query = $mapper->createQuery();
                $mapper->deleteByQuery($query);
            }
        }
    }

    static public function setUpBeforeClass()
    {
        MongoMapper::setServiceManager(self::$sm);
    }

    static public function tearDownAfterClass()
    {
        foreach (self::$sm->get('MongoManager')->getAll() as $mongoDb) {
            $mongoDb->drop();
        }
    }

    /**
     * @static
     * @param array $config
     */
    final static public function setConfig($config)
    {
        self::$sm = new ZendServiceManager(
            new ZendServiceManagerConfig(array(
                'factories' => array(
                    'MongoManager' => '\GeometriaLab\Mongo\ServiceFactory',
                    'Configuration' => function($sm) use ($config) {
                        return new ZendConfig($config);
                    },
                ),
            ))
        );
    }
}
