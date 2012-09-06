<?php

namespace GeometriaLab\Api\View\Http;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface as ZendEvents;
use Zend\Mvc\MvcEvent as ZendMvcEvent;

use GeometriaLab\Model,
    GeometriaLab\Api\View\Model\ApiModel,
    GeometriaLab\Stdlib\Extractor\Fields;

/**
 *
 */
class CreateApiModelListener implements ZendListenerAggregateInterface
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
        $this->listeners[] = $events->attach('dispatch', array($this, 'createApiModel'),  -99);
        $this->listeners[] = $events->attach('dispatch', array($this, 'hydrateData'),  1);
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
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function createApiModel(ZendMvcEvent $e)
    {
        $result = $e->getResult();

        if ($result instanceof ApiModel) {
            $apiModel = $result;
        } else {
            $apiModel = new ApiModel(array(
                ApiModel::FIELD_DATA => $result
            ));
        }

        // set data (if not set yet)
        if ($apiModel->getVariable(ApiModel::FIELD_DATA, false) === false) {
            $apiModel->setVariable(ApiModel::FIELD_DATA, null);
        }

        $response = $e->getResponse();
        $apiException = $e->getParam('apiException', false);

        // set http code
        $httpCode = $response->getStatusCode();
        $apiModel->setVariable(ApiModel::FIELD_HTTPCODE, $httpCode);

        // set status
        if ($response->isClientError()) {
            $status = ApiModel::STATUS_ERROR;
        } else if ($response->isServerError()) {
            $status = ApiModel::STATUS_FAIL;
        } else {
            $status = ApiModel::STATUS_SUCCESS;
        }
        $apiModel->setVariable(ApiModel::FIELD_STATUS, $status);

        // set error code and message (if any)
        if ($apiException) {
            $errorCode = $apiException->getErrorCode();
            $errorMessage = $apiException->getErrorMessage();
        } else {
            $errorCode = null;
            $errorMessage = null;
        }
        $apiModel->setVariable(ApiModel::FIELD_ERRORCODE, $errorCode);
        $apiModel->setVariable(ApiModel::FIELD_ERRORMESSAGE, $errorMessage);

        $e->setResult($apiModel);
    }

    /**
     * @param ZendMvcEvent $e
     */
    public function hydrateData(ZendMvcEvent $e)
    {
        $result = $e->getResult();

        if ($result instanceof ApiModel) {
            $data = $result->getVariable(ApiModel::FIELD_DATA);
        } else {
            $data = $result;
        }

        if ($data instanceof Model\ModelInterface || $data instanceof Model\CollectionInterface) {
            $fields = Fields::createFromString($e->getRequest()->getQuery()->get('_fields'));
            $data = $e->getApplication()->getServiceManager()->get('Extractor')->extract($data, $fields);

            if ($result instanceof ApiModel) {
                $result->setVariable(ApiModel::FIELD_DATA, $data);
            } else {
                $result = $data;
            }

            $e->setResult($result);
        }
    }
}
