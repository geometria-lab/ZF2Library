<?php

namespace GeometriaLab\Mvc\View\Http;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface as ZendEventManagerInterface;
use Zend\Mvc\MvcEvent as ZendMvcEvent;
use Zend\Mvc\Application as ZendApplication;
use Zend\Http\Response as ZendHttpResponse;

class HandleExceptionStrategy implements ZendListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    public function attach(ZendEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(ZendMvcEvent::EVENT_DISPATCH_ERROR, array($this, 'detectNotFoundError'));
    }

    public function detach(ZendEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function detectException(ZendMvcEvent $e)
    {
        $error = $e->getError();
        if (empty($error)) {
            return;
        }

        $exception = $e->getParam('exception');
        if (!$exception instanceof \Exception) {
            return;
        }

        switch ($error) {
            case ZendApplication::ERROR_CONTROLLER_NOT_FOUND:
            case ZendApplication::ERROR_CONTROLLER_INVALID:
            case ZendApplication::ERROR_ROUTER_NO_MATCH:
                $apiException = new \GeometriaLab\Api\Exception\ResourceNotFound();
                break;
            default:
                if (!$exception instanceof \GeometriaLab\Api\Exception\Exception) {
                    $apiException = new \GeometriaLab\Api\Exception\ServerError();
                } else {
                    $apiException = $exception;
                }
                break;
        }

        $response = $e->getResponse();
        if (!$response) {
            $response = new ZendHttpResponse();
            $e->setResponse($response);
        }

        $response->setStatusCode($apiException->getHttpCode());
        $e->setParam('apiException', $apiException);
    }
}
