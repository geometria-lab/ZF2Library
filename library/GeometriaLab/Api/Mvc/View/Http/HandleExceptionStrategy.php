<?php

namespace GeometriaLab\Api\Mvc\View\Http;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface as ZendEventManagerInterface;
use Zend\Mvc\MvcEvent as ZendMvcEvent;
use Zend\Mvc\Application as ZendApplication;
use Zend\Http\Response as ZendHttpResponse;

use GeometriaLab\Api\Exception as ApiException;

/**
 *
 */
class HandleExceptionStrategy implements ZendListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @param \Zend\EventManager\EventManagerInterface $events
     */
    public function attach(ZendEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(ZendMvcEvent::EVENT_DISPATCH_ERROR, array($this, 'detectException'));
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $events
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
     * @param \Zend\Mvc\MvcEvent $e
     */
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

        $config = $e->getApplication()->getServiceManager()->get('Config');
        if (!empty($config['throwExceptions'])) {
            throw $exception;
        }

        switch ($error) {
            case ZendApplication::ERROR_CONTROLLER_NOT_FOUND:
            case ZendApplication::ERROR_CONTROLLER_INVALID:
            case ZendApplication::ERROR_ROUTER_NO_MATCH:
                $apiException = new ApiException\ResourceNotFoundException();
                break;
            default:
                if (!$exception instanceof ApiException\AbstractException) {
                    $apiException = new ApiException\ServerErrorException();
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
