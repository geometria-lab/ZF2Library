<?php

namespace GeometriaLab\View\Http;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface as Events;
use Zend\Mvc\MvcEvent as ZendMvcEvent;

use GeometriaLab\View\Model\ApiModel;
use GeometriaLab\Model;

//use Zend\Stdlib\ArrayUtils;
//use Zend\View\Model\ViewModel;

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
     * @param  Events $events
     * @return void
     */
    public function attach(Events $events)
    {
        $this->listeners[] = $events->attach('dispatch', array($this, 'createApiModel'),  -99);
        $this->listeners[] = $events->attach('dispatch', array($this, 'hydrateData'),  1);
    }

    /**
     * Detach listeners
     *
     * @param  Events $events
     * @return void
     */
    public function detach(Events $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

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
        if ($apiModel->getVariable(ApiModel::FIELD_DATA) === null) {
            $apiModel->setVariable(ApiModel::FIELD_DATA, '');
        }

        $response = $e->getResponse();
        $apiException = $e->getParam('apiException', false);

        if ($apiException) {
            $httpCode = $apiException->getHttpStatusCode();
        } else {
            $httpCode = $response->getStatusCode();
        }

        // set code
        $apiModel->setVariable(ApiModel::FIELD_CODE, $httpCode);

        // set status
        if ($response->isClientError()) {
            $status = ApiModel::STATUS_ERROR;
        } else if ($response->isServerError()) {
            $status = ApiModel::STATUS_FAIL;
        } else {
            $status = ApiModel::STATUS_SUCCESS;
        }
        $apiModel->setVariable(ApiModel::FIELD_STATUS, $status);

        // set message
        if ($apiException) {
            $apiCode = $apiException->getApiCode();
        } else {
            $apiCode = $apiModel->getVariable(ApiModel::FIELD_MESSAGE, '');
        }

        $apiModel->setVariable(ApiModel::FIELD_MESSAGE, $apiCode);

        $e->setResult($apiModel);
    }

    public function hydrateData(ZendMvcEvent $e)
    {
        $result = $e->getResult();

        if ($result instanceof ApiModel) {
            $data = $result->getVariable(ApiModel::FIELD_DATA);
        } else {
            $data = $result;
        }

        if ($data instanceof Model\ModelInterface || $data instanceof Model\CollectionInterface) {
            // $data = hydrate($data);

            if ($result instanceof ApiModel) {
                $result->setVariable(ApiModel::FIELD_DATA, $data);
            } else {
                $result = $data;
            }

            $e->setResult($result);
        }
    }
}
