<?php

namespace GeometriaLabTest\Api\Mvc\Controller\Action\Params;

use GeometriaLabTest\Api\Mvc\Controller\Action\Params\TestModel\TestModel;

use GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\ArrayProperty as ParamsArrayProperty,
    GeometriaLab\Api\Mvc\Controller\Action\Params\Listener as ParamsListener,
    GeometriaLab\Model\Schema\Property\ModelProperty;

use Zend\Config\Config as ZendConfig,
    Zend\Http\Request as ZendRequest,
    Zend\Http\Response as ZendResponse,
    Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\Mvc\Router\RouteMatch as ZendRouteMatch,
    Zend\Mvc\Service\ServiceManagerConfig as ZendServiceManagerConfig,
    Zend\ServiceManager\ServiceManager as ZendServiceManager;

class ParamsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestModel
     */
    static $model;
    /**
     * @var ZendServiceManager
     */
    static private $sm;
    /**
     * @var ZendMvcEvent
     */
    static private $event;

    static public function setUpBeforeClass()
    {
        static::$model = new TestModel(array(
            'id'                => '1',
            'stringProperty'    => 'Foo',
        ));
        static::$model->save();
    }

    public function setUp()
    {
        static::$sm = new ZendServiceManager(
            new ZendServiceManagerConfig(array(
                'invokables' => array(
                    'DispatchListener' => 'Zend\Mvc\DispatchListener',
                    'Request'          => 'Zend\Http\PhpEnvironment\Request',
                    'Response'         => 'Zend\Http\PhpEnvironment\Response',
                    'RouteListener'    => 'Zend\Mvc\RouteListener',
                    'ViewManager'      => 'ZendTest\Mvc\TestAsset\MockViewManager'
                ),
                'factories' => array(
                    'ServiceListener'         => 'Zend\Mvc\Service\ServiceListenerFactory',
                    'ControllerLoader'        => 'Zend\Mvc\Service\ControllerLoaderFactory',
                    'ControllerPluginManager' => 'Zend\Mvc\Service\ControllerPluginManagerFactory',
                    'Application'             => 'Zend\Mvc\Service\ApplicationFactory',
                    'HttpRouter'              => 'Zend\Mvc\Service\RouterFactory',
                    'Params'                  => 'GeometriaLab\Api\Mvc\Controller\Action\Params\ServiceFactory',
                    'Config'                  => function($sm) {
                        return array(
                            'params' => array(
                                '__NAMESPACE__' => '\GeometriaLabTest\Api\Mvc\Controller\Action\Params',
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

        $request = new ZendRequest();
        $request->setMethod('GET');

        $routeMatch = new ZendRouteMatch(array(
            '__CONTROLLER__' => 'Sample',
            'action' => 'test'
        ));

        static::$event = new ZendMvcEvent();
        static::$event->setApplication(static::$sm->get('Application'));
        static::$event->setRequest($request);
        static::$event->setResponse(new ZendResponse());
        static::$event->setRouteMatch($routeMatch);
        /* @var \Zend\Mvc\Application $application */
        $application = static::$sm->get('Application');
        $application->bootstrap();
        $application->getMvcEvent()->setRouteMatch($routeMatch);
    }

    static public function tearDownAfterClass()
    {
        TestModel::getMapper()->deleteByQuery(TestModel::getMapper()->createQuery());
    }

    public function testCreateParams()
    {
        static::$event->getRouteMatch()->setParam('id', '1');
        static::$event->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array(
            'name' => 'Phillip',
            'email' => 'fry@example.com',
            'float' => '12.3',
            'array' => array('foo', '123'),
            'bool' => true
        )));
        
        $params = new ParamsListener();
        $params->createParams(static::$event);

        /* @var Sample\Test $params */
        $params = static::$event->getrouteMatch()->getParam('params');

        $this->assertInstanceOf('\GeometriaLab\Api\Mvc\Controller\Action\Params\AbstractParams', $params);

        $this->assertInstanceOf('\GeometriaLabTest\Api\Mvc\Controller\Action\Params\Sample\Test', $params);

        $this->assertEquals(array(
                'id' => '1',
                'array' => array('foo', '123'),
                'float' => '12.3',
                'bool' => true,
                'name' => 'Phillip',
                'email' => 'fry@example.com',
                'defaultProperty' => 'Bar',
                'relationModel' => static::$model,
            ),
            $params->toArray()
        );
    }

    public function testNotFilterProperty()
    {
        static::$event->getRouteMatch()->setParam('id', '1');
        static::$event->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array(
            'name' => ' Phillip ',
            'email' => 'Fry@Example.com',
            'float' => '12.3',
            'array' => array('foo', '123'),
            'bool' => true,
        )));

        $params = new ParamsListener();
        $params->createParams(static::$event);

        /* @var Sample\Test $params */
        $params = static::$event->getrouteMatch()->getParam('params');

        $this->assertEquals(array(
                'id' => '1',
                'array' => array('foo', '123'),
                'float' => '12.3',
                'bool' => true,
                'name' => 'Phillip',
                'email' => 'fry@example.com',
                'defaultProperty' => 'Bar',
                'relationModel' => static::$model,
            ),
            $params->toArray()
        );
    }

    public function testInvalidProperty()
    {
        $this->setExpectedException('GeometriaLab\Api\Exception\InvalidParamsException');

        static::$event->getRouteMatch()->setParam('id', '1');
        static::$event->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array(
            'name' => 'Phillip',
            'email' => 'Fry@example.com',
            'float' => '12.3',
            'array' => array('foo', '123'),
            'bool' => 'foo',
        )));

        $params = new ParamsListener();
        $params->createParams(static::$event);
    }

    public function testNotPresentProperty()
    {
        $this->setExpectedException('GeometriaLab\Api\Exception\InvalidParamsException');

        static::$event->getRouteMatch()->setParam('id', '1');
        static::$event->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array(
            'name' => 'Phillip',
            'email' => 'Fry@example.com',
            'float' => '12.3',
            'array' => array('foo', '123'),
            'bool' => true,
            'invalid' => 'foo'
        )));

        $params = new ParamsListener();
        $params->createParams(static::$event);
    }

    public function testRequiredProperty()
    {
        $this->setExpectedException('GeometriaLab\Api\Exception\InvalidParamsException');

        static::$event->getRouteMatch()->setParam('id', '1');
        static::$event->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array(
            'name' => 'Phillip',
            'email' => 'Fry@example.com',
            'float' => '12.3',
            'bool' => true,
            'invalid' => 'foo'
        )));

        $params = new ParamsListener();
        $params->createParams(static::$event);
    }

    public function testNotValidProperty()
    {
        $this->setExpectedException('GeometriaLab\Api\Exception\InvalidParamsException');

        static::$event->getRouteMatch()->setParam('id', '1');
        static::$event->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array(
            'name' => 'Phillip',
            'email' => 'Bad email',
            'float' => '12.3',
            'array' => array('foo', '123'),
            'bool' => true,
        )));

        $params = new ParamsListener();
        $params->createParams(static::$event);
    }

    public function testUnsetSpecialParams()
    {
        static::$event->getRouteMatch()->setParam('id', '1');
        static::$event->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array(
            'name' => ' Phillip ',
            'email' => 'Fry@Example.com',
            'float' => '12.3',
            'array' => array('foo', '123'),
            'bool' => true,
            '_method' => 'get',
        )));

        $params = new ParamsListener();
        $params->createParams(static::$event);

        /* @var Sample\Test $params */
        $params = static::$event->getrouteMatch()->getParam('params');

        $this->assertEquals(array(
                'id' => '1',
                'array' => array('foo', '123'),
                'float' => '12.3',
                'bool' => true,
                'name' => 'Phillip',
                'email' => 'fry@example.com',
                'defaultProperty' => 'Bar',
                'relationModel' => static::$model,
            ),
            $params->toArray()
        );
    }

    public function testBadId()
    {
        $this->setExpectedException('GeometriaLab\Api\Exception\ObjectNotFoundException');

        static::$event->getRouteMatch()->setParam('id', '2');
        static::$event->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array(
            'name' => ' Phillip ',
            'email' => 'Fry@Example.com',
            'float' => '12.3',
            'array' => array('foo', '123'),
            'bool' => true,
        )));

        $params = new ParamsListener();
        $params->createParams(static::$event);
    }

    public function testModelPropertySet()
    {
        $this->setExpectedException('\RuntimeException', "Property 'modelProperty' must implement 'GeometriaLab\\Api\\Mvc\\Controller\\Action\\Params\\Schema\\Property\\PropertyInterface' interface, but 'GeometriaLab\\Model\\Schema\\Property\\PropertyInterface");

        static::$event->getRouteMatch()->setParam('id', '1');
        static::$event->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array(
            'name' => ' Phillip ',
            'email' => 'Fry@Example.com',
            'float' => '12.3',
            'array' => array('foo', '123'),
            'bool' => true,
        )));

        $params = new ParamsListener();
        $params->createParams(static::$event);

        /* @var Sample\Test $params */
        $params = static::$event->getrouteMatch()->getParam('params');

        $model = new ModelProperty(array('name' => 'modelProperty'));
        $params->getSchema()->addProperty($model);
    }

    public function testArrayOfModelsPropertySet()
    {
        $this->setExpectedException('\RuntimeException', "Item of array property must be an instance of \\GeometriaLab\\Api\\Mvc\\Controller\\Action\\Params\\Schema\\Property\\PropertyInterface");

        static::$event->getRouteMatch()->setParam('id', '1');
        static::$event->getRequest()->setQuery(new \Zend\Stdlib\Parameters(array(
            'name' => ' Phillip ',
            'email' => 'Fry@Example.com',
            'float' => '12.3',
            'array' => array('foo', '123'),
            'bool' => true,
        )));

        $params = new ParamsListener();
        $params->createParams(static::$event);

        /* @var Sample\Test $params */
        $params = static::$event->getrouteMatch()->getParam('params');

        $arrayProperty = new ParamsArrayProperty(array(
            'name'         => 'arrayProperty',
            'itemProperty' => new ModelProperty(),
        ));

        $params->getSchema()->addProperty($arrayProperty);
    }
}