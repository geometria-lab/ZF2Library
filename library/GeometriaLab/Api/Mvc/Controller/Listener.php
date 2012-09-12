<?php

namespace GeometriaLab\Api\Mvc\Controller;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface,
    Zend\EventManager\EventManagerInterface as ZendEvents,
    Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\Stdlib\RequestInterface as ZendRequestInterface,
    Zend\Mvc\Exception\DomainException as ZendDomainException;

use GeometriaLab\Api\Mvc\Controller\Action\Params,
    GeometriaLab\Api\Exception\WrongFields;

class Listener implements ZendListenerAggregateInterface
{
    /**
     * Listeners we've registered
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Attach listeners
     *
     * @param  ZendEvents $events
     * @return void
     */
    public function attach(ZendEvents $events)
    {
        $this->listeners[] = $events->attach(ZendMvcEvent::EVENT_ROUTE, array($this, 'prepareAction'),  -20);
        $this->listeners[] = $events->attach(ZendMvcEvent::EVENT_ROUTE, array($this, 'createParams'),  -30);
    }

    /**
     * Detach listeners
     *
     * @param  ZendEvents $events
     * @return void
     */
    public function detach(ZendEvents $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param ZendMvcEvent $e
     * @throws ZendDomainException
     */
    public function prepareAction(ZendMvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $request = $e->getRequest();

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
                    throw new ZendDomainException('Post is allowed on resources only');
                }
                if (null !== $subResource) {
                    $action = 'create' . ucfirst($subResource);
                } else {
                    $action = 'create';
                }
                break;
            case 'put':
                if (null === $id) {
                    throw new ZendDomainException('Missing identifier');
                }
                if (null !== $subResource) {
                    throw new ZendDomainException('Put is allowed on root resource object only');
                }
                $action = 'update';
                break;
            case 'delete':
                if (null === $id) {
                    throw new ZendDomainException('Missing identifier');
                }
                if (null !== $subResource) {
                    throw new ZendDomainException('Delete is allowed on root resource object only');
                }
                $action = 'delete';
                break;
            default:
                throw new ZendDomainException('Invalid HTTP method!');
        }

        $routeMatch->setParam('action', $action);
    }

    /**
     * @param ZendMvcEvent $e
     * @throws WrongFields
     */
    public function createParams(ZendMvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $request = $e->getRequest();
        $queryParams = self::getParamsFromRequest($request);

        /* @var Params $params */
        $params = $e->getApplication()->getServiceManager()->get('Params');

        try {
            $params->populate($queryParams);
        } catch (\InvalidArgumentException $ex) {
            throw new WrongFields($ex->getMessage());
        };

        $routeMatch->setParam('params', $params);
    }

    /**
     * @static
     * @param ZendRequestInterface $request
     * @return mixed
     */
    static public function getParamsFromRequest(ZendRequestInterface $request)
    {
        $queryParams = $request->getQuery()->toArray();

        unset($queryParams['q']);

        foreach ($queryParams as $key => $value) {
            if ($key[0] == '_') {
                unset($queryParams[$key]);
            }
        }

        return $queryParams;
    }
}
