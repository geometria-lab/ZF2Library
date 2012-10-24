<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface,
    Zend\EventManager\EventManagerInterface as ZendEventManagerInterface,
    Zend\Mvc\Router\RouteMatch as ZendRouteMatch,
    Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\Http\Request as ZendRequest;

use GeometriaLab\Api\Exception\InvalidParamsException,
    GeometriaLab\Api\Exception\ObjectNotFoundException;

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
     * @param ZendEventManagerInterface $events
     * @return void
     */
    public function attach(ZendEventManagerInterface $events)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', ZendMvcEvent::EVENT_DISPATCH, array($this, 'createParams'), 100);
    }

    /**
     * Detach listeners
     *
     * @param ZendEventManagerInterface $events
     * @return void
     */
    public function detach(ZendEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Create Params object and inject it to RouteMatch
     *
     * @param ZendMvcEvent $e
     * @throws InvalidParamsException
     */
    public function createParams(ZendMvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $request = $e->getRequest();
        $requestParams = $this->getParamsFromRequest($request);

        // @TODO Stub
        $id = $routeMatch->getParam('id');
        if ($id !== null) {
            $requestParams['id'] = $id;
        }

        /* @var AbstractParams $params */
        $params = $e->getApplication()->getServiceManager()->get('Params');
        $params->populate($requestParams);

        $this->validateParams($params, $routeMatch);

        $routeMatch->setParam('params', $params);
    }

    /**
     * Get params from request
     *
     * @param ZendRequest $request
     * @return mixed
     */
    protected function getParamsFromRequest(ZendRequest $request)
    {
        $params = array_merge(
            $request->getQuery()->toArray(),
            $request->getPost()->toArray()
        );

        foreach ($params as $key => $value) {
            if ($key[0] == '_') {
                unset($params[$key]);
            }
        }

        unset($params['access_token']);

        return $params;
    }

    /**
     * Validate Params object
     *
     * @param AbstractParams $params
     * @param ZendRouteMatch $routeMatch
     * @throws \RuntimeException
     * @throws ObjectNotFoundException
     * @throws InvalidParamsException
     */
    public function validateParams(AbstractParams $params, ZendRouteMatch $routeMatch)
    {
        if ($routeMatch->getParam('id') !== null) {
            if (!$params->has('id')) {
                throw new \RuntimeException("Need 'id' property");
            }

            $hasRelationObject = false;

            foreach ($params->getRelations() as $relation) {
                /* @var \GeometriaLab\Model\Persistent\Relation\BelongsTo $relation */
                if ($relation->getProperty()->getOriginProperty() == 'id') {
                    $relationName = $relation->getProperty()->getName();
                    if ($params->$relationName === null) {
                        throw new ObjectNotFoundException();
                    }
                    $hasRelationObject = true;
                    break;
                }
            }

            if (!$hasRelationObject) {
                throw new \RuntimeException('Need belongsTo relation with originProperty = id');
            }
        }

        if (!$params->isValid()) {
            $exception = new InvalidParamsException();
            $exception->setParams($params);

            throw $exception;
        }
    }
}
