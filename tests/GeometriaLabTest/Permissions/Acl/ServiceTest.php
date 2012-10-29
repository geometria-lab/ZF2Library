<?php

namespace GeometriaLabTest\Permissions\Acl;

use GeometriaLab\Permissions\Acl\ServiceFactory as AclServiceFactory;

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
     * @var \Zend\Permissions\Acl\Acl
     */
    static private $acl;

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
                            'acl' => array(
                                'roles' => array(
                                    'guest',
                                    array(
                                        'name' => 'user',
                                        'parent' => 'guest',
                                    ),
                                ),
                                'base_dir'      => __DIR__ . '/Sample',
                                '__NAMESPACE__' => 'GeometriaLabTest\Permissions\Acl\Sample',
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

        $serviceFactory = new AclServiceFactory();
        static::$acl = $serviceFactory->createService(static::$serviceManager);
    }

    public function testStaticRoles()
    {
        $this->assertTrue(static::$acl->hasRole('guest'));
        $this->assertTrue(static::$acl->hasRole('user'));
    }

    public function testInheritStaticRole()
    {
        $this->assertTrue(static::$acl->inheritsRole('user', 'guest'));
    }

    public function testDynamicRoles()
    {
        $this->assertTrue(static::$acl->hasRole('moderator'));
        $this->assertTrue(static::$acl->hasRole('cityManager'));
    }

    public function testResources()
    {
        $this->assertTrue(static::$acl->hasResource('Sample\Users'));
        $this->assertTrue(static::$acl->hasResource('Sample\Cities'));
    }

    public function testStaticAssert()
    {
        $this->assertTrue(static::$acl->isAllowed('moderator', 'Sample\Users', 'assert'));
        $this->assertFalse(static::$acl->isAllowed('moderator', 'Sample\Users', 'foo'));
    }

    public function testDynamicAssert()
    {
        $this->assertTrue(static::$acl->isAllowed('moderator', 'Sample\Users', 'dynamic'));
    }

    public function testDynamicAssertWithoutPrivilege()
    {
        $this->assertFalse(static::$acl->isAllowed('moderator', 'Sample\Users'));
    }

    public function testBadDynamicAssert()
    {
        $this->setExpectedException('\InvalidArgumentException');

        static::$acl->isAllowed('moderator', 'Sample\Users', 'notDynamic');
    }
}
