<?php

namespace GeometriaLab\Api\Mvc\Controller;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface,
    Zend\EventManager\EventManagerInterface as ZendEvents,
    Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\Stdlib\RequestInterface as ZendRequestInterface;

use GeometriaLab\Api\Mvc\Controller\Action\Params\Params,
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
     * @throws WrongFields
     */
    public function createParams(ZendMvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $request = $e->getRequest();
        $queryParams = self::getParamsFromRequest($request);

        // @TODO Stub
        $id = $routeMatch->getParam('id');
        if ($id !== null) {
            $queryParams['id'] = $id;
        }

        /* @var Params $params */
        $params = $e->getApplication()->getServiceManager()->get('Params');
        $params->populateSilent($queryParams, false);

        if (!$params->isValid()) {
            $errorString = '';
            foreach ($params->getErrorMessages() as $fieldName => $errors) {
                $errorString .= "Field $fieldName:\r\n" . implode("\r\n", $errors) . "\r\n";
            }
            throw new WrongFields($errorString);
        }

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
