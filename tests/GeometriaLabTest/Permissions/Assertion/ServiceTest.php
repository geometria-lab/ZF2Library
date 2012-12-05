<?php

namespace GeometriaLabTest\Permissions\Assertion;

use GeometriaLab\Permissions\Assertion\Assertion,
    GeometriaLab\Permissions\Assertion\Service;

use Zend\ServiceManager\ServiceManager as ZendServiceManager,
    Zend\Mvc\Service\ServiceManagerConfig as ZendServiceManagerConfig,
    Zend\Mvc\Router\Http\RouteMatch as ZendRouteMatch;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ZendServiceManager
     */
    static private $serviceManager;

    /**
     * @var Assertion
     */
    static private $assertion;

    static public function setUpBeforeClass()
    {
        static::$serviceManager = new ZendServiceManager(
            new ZendServiceManagerConfig(array(
                'invokables' => array(
                    'DispatchListener' => 'Zend\Mvc\DispatchListener',
                    'Request'          => 'Zend\Http\PhpEnvironment\Request',
                    'Response'         => 'Zend\Http\PhpEnvironment\Response',
                    'RouteListener'    => 'Zend\Mvc\RouteListener',
                    'ViewManager'      => 'ZendTest\Mvc\TestAsset\MockViewManager'
                ),
                'factories' => array(
                    'ServiceListener'           => 'Zend\Mvc\Service\ServiceListenerFactory',
                    'ControllerLoader'          => 'Zend\Mvc\Service\ControllerLoaderFactory',
                    'ControllerPluginManager'   => 'Zend\Mvc\Service\ControllerPluginManagerFactory',
                    'Application'               => 'Zend\Mvc\Service\ApplicationFactory',
                    'HttpRouter'                => 'Zend\Mvc\Service\RouterFactory',
                    'Config'                    => function($e){
                        return array(
                            'assertion' => array(
                                'base_dir'      => __DIR__ . '/SampleResource',
                                '__NAMESPACE__' => 'GeometriaLabTest\Permissions\Assertion\SampleResource',
                            ),
                        );
                    },
                ),
                'aliases' => array(
                    'Router'        => 'HttpRouter',
                    'Configuration' => 'Config',
                ),
            ))
        );

        static::$serviceManager->get('Application')->bootstrap();
        static::$serviceManager->get('Application')->getMvcEvent()->setRouteMatch(new ZendRouteMatch(array(
            '__NAMESPACE__' => 'Sample',
        )));

        $serviceFactory = new Service();
        static::$assertion = $serviceFactory->createService(static::$serviceManager);
    }

    public function testCreateService()
    {
        $this->assertInstanceOf('\\GeometriaLab\\Permissions\\Assertion\\Assertion', static::$assertion);
    }

    public function testAddResources()
    {
        $expected = array(
            'Foo' => new SampleResource\Foo('Foo'),
            'Bar' => new SampleResource\Bar('Bar'),
        );

        $this->assertEquals($expected, static::$assertion->getResources());
    }
}
