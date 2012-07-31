<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 27.07.12
 * Time: 15:28
 * To change this template use File | Settings | File Templates.
 */
namespace GeometriaLab\Mvc\Controller;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface as Event;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Mvc\Exception;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

abstract class AbstractRestfulController implements
    Dispatchable,
    EventManagerAwareInterface,
    InjectApplicationEventInterface,
    ServiceLocatorAwareInterface
{
    /**
     * @var PluginManager
     */
    protected $plugins;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Event
     */
    protected $event;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var ServiceLocatorInterface
     */
    protected $locator;

    /**
     * Return list of resources
     *
     * @param Action\Params $params
     * @return mixed
     */
    abstract public function getList(Action\Params $params);

    /**
     * Return single resource
     *
     * @param Action\Params $params
     * @return mixed
     */
    abstract public function get(Action\Params $params);

    /**
     * Create a new resource
     *
     * @param Action\Params $params
     * @return mixed
     */
    abstract public function create(Action\Params $params);

    /**
     * Update an existing resource
     *
     * @param Action\Params $params
     * @return mixed
     */
    abstract public function update(Action\Params $params);

    /**
     * Delete an existing resource
     *
     * @param Action\Params $params
     * @return mixed
     */
    abstract public function delete(Action\Params $params);

    /**
     * Basic functionality for when a page is not available
     *
     * @return array
     */
    public function notFoundAction()
    {
        $this->response->setStatusCode(404);
        return array('content' => 'Page not found');
    }

    /**
     * Dispatch a request
     *
     * If the route match includes an "action" key, then this acts basically like
     * a standard action controller. Otherwise, it introspects the HTTP method
     * to determine how to handle the request, and which method to delegate to.
     *
     * @events dispatch.pre, dispatch.post
     * @param  Request $request
     * @param  null|Response $response
     * @return mixed|Response
     * @throws Exception\InvalidArgumentException
     */
    public function dispatch(Request $request, Response $response = null)
    {
        if (!$request instanceof HttpRequest) {
            throw new Exception\InvalidArgumentException('Expected an HTTP request');
        }
        $this->request = $request;
        if (!$response) {
            $response = new HttpResponse();
        }
        $this->response = $response;

        $e = $this->getEvent();
        $e->setRequest($request)
          ->setResponse($response)
          ->setTarget($this);

        $result = $this->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH, $e, function($test) {
            return ($test instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        return $e->getResult();
    }

    public function execute(MvcEvent $e)
        {
            $routeMatch = $e->getRouteMatch();
            if (!$routeMatch) {
                /**
                 * @todo Determine requirements for when route match is missing.
                 *       Potentially allow pulling directly from request metadata?
                 */
                throw new \DomainException('Missing route matches; unsure how to retrieve action');
            }

            $request = $e->getRequest();
            $action  = $routeMatch->getParam('action', false);
            if ($action) {
                // Handle arbitrary methods, ending in Action
                $method = static::getMethodFromAction($action);
                if (!method_exists($this, $method)) {
                    $method = 'notFoundAction';
                }
                $return = $this->$method();
            } else {
                // RESTful methods

                $id = $routeMatch->getParam('id');
                if ($id === null) {
                    $id = $request->getQuery()->get('id');
                }

                $subResource = $routeMatch->getParam('subResource');

                switch (strtolower($request->getMethod())) {
                    case 'get':
                        if (null !== $id) {
                            if (null !== $subResource) {
                                $action = 'get' . ucfirst($subResource);
                            } else {
                                $action = 'get';
                            }
                        } else {
                            if (null !== $subResource) {
                                $action = 'get' . ucfirst($subResource) . 'List';
                            } else {
                                $action = 'getList';
                            }
                        }
                        break;
                    case 'post':
                        if (null !== $id) {
                            throw new \DomainException('Post is allowed on resources only');
                        }
                        if (null !== $subResource) {
                            $action = 'create' . ucfirst($subResource);
                        } else {
                            $action = 'create';
                        }
                        break;
                    case 'put':
                        if (null === $id) {
                            throw new \DomainException('Missing identifier');
                        }
                        if (null !== $subResource) {
                            throw new \DomainException('Put is allowed on root resource object only');
                        }
                        $action = 'update';
                        break;
                    case 'delete':
                        if (null === $id) {
                            throw new \DomainException('Missing identifier');
                        }
                        if (null !== $subResource) {
                            throw new \DomainException('Delete is allowed on root resource object only');
                        }
                        $action = 'delete';
                        break;
                    default:
                        throw new \DomainException('Invalid HTTP method!');
                }

                $routeMatch->setParam('action', $action);

                $params = $this->getServiceLocator()->get('ParamsLoader')->getByRouteMatch($routeMatch);

                if ($id) {
                    $params->id = (int)$id;
                }

                $return = $this->$action($params);


            }

            // Emit post-dispatch signal, passing:
            // - return from method, request, response
            // If a listener returns a response object, return it immediately
            $e->setResult($return);
            return $return;
        }

    /**
     * Get request object
     *
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new HttpRequest();
        }
        return $this->request;
    }

    /**
     * Get response object
     *
     * @return Response
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = new HttpResponse();
        }
        return $this->response;
    }

    /**
     * Set the event manager instance used by this context
     *
     * @param  EventManagerInterface $events
     * @return AbstractRestfulController
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            'Zend\Stdlib\DispatchableInterface',
            __CLASS__,
            get_called_class()
        ));
        $this->events = $events;
        $this->attachDefaultListeners();
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    /**
     * Set an event to use during dispatch
     *
     * By default, will re-cast to MvcEvent if another event type is provided.
     *
     * @param  Event $e
     * @return void
     */
    public function setEvent(Event $e)
    {
        if ($e instanceof Event && !$e instanceof MvcEvent) {
            $eventParams = $e->getParams();
            $e = new MvcEvent();
            $e->setParams($eventParams);
            unset($eventParams);
        }
        $this->event = $e;
    }

    /**
     * Get the attached event
     *
     * Will create a new MvcEvent if none provided.
     *
     * @return MvcEvent
     */
    public function getEvent()
    {
        if (!$this->event) {
            $this->setEvent(new MvcEvent());
        }
        return $this->event;
    }

    /**
     * Set locator instance
     *
     * @param  ServiceLocatorInterface $locator
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Retrieve locator instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->locator;
    }

    /**
     * Get plugin manager
     *
     * @return PluginManager
     */
    public function getPluginManager()
    {
        if (!$this->plugins) {
            $this->setPluginManager(new PluginManager());
        }
        return $this->plugins;
    }

    /**
     * Set plugin manager
     *
     * @param  string|PluginManager $plugins
     * @return RestfulController
     * @throws Exception\InvalidArgumentException
     */
    public function setPluginManager(PluginManager $plugins)
    {
        $this->plugins = $plugins;
        if (method_exists($plugins, 'setController')) {
            $this->plugins->setController($this);
        }
        return $this;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $name    Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return mixed
     */
    public function plugin($name, array $options = null)
    {
        return $this->getPluginManager()->get($name, $options);
    }

    /**
     * Method overloading: return/call plugins
     *
     * If the plugin is a functor, call it, passing the parameters provided.
     * Otherwise, return the plugin instance.
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $params);
        }
        return $plugin;
    }

    /**
     * Register the default events for this controller
     *
     * @return void
     */
    protected function attachDefaultListeners()
    {
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'execute'));
    }

    /**
     * Transform an "action" token into a method name
     *
     * @param  string $action
     * @return string
     */
    public static function getMethodFromAction($action)
    {
        $method  = str_replace(array('.', '-', '_'), ' ', $action);
        $method  = ucwords($method);
        $method  = str_replace(' ', '', $method);
        $method  = lcfirst($method);
        $method .= 'Action';
        return $method;
    }
}
