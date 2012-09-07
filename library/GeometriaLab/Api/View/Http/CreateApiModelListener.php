<?php

namespace GeometriaLab\Api\View\Http;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface as ZendEvents;
use Zend\Mvc\MvcEvent as ZendMvcEvent;

use GeometriaLab\Model,
    GeometriaLab\Model\ModelInterface,
    GeometriaLab\Api\View\Model\ApiModel,
    GeometriaLab\Api\Exception\WrongFields;

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
            $fields = $e->getRequest()->getQuery()->get('_fields');
            $fieldsData = self::createFieldsFromString($fields);

            $extractedData = $e->getApplication()->getServiceManager()->get('Extractor')->extract($data, $fieldsData);

            if ($result instanceof ApiModel) {
                $result->setVariable(ApiModel::FIELD_DATA, $extractedData);
            } else {
                $result = $extractedData;
            }

            $e->setResult($result);
        }
    }

    /**
     * Create Fields from string
     *
     * @static
     * @param $fieldsString
     * @return array
     * @throws WrongFields
     */
    static public function createFieldsFromString($fieldsString)
    {
        $fieldsString = str_replace(' ', '', $fieldsString);
        $fields = array();
        $level = 0;
        $stack = array();
        $stack[$level] = &$fields;
        $field = '';
        $len = strlen($fieldsString) - 1;

        for ($i = 0; $i <= $len; $i++) {
            $char = $fieldsString[$i];
            switch ($char) {
                case ',':
                    if ('' != $field) {
                        $stack[$level][$field] = true;
                        $field = '';
                    }
                    break;
                case '(':
                    $stack[$level][$field] = array();
                    $oldLevel = $level;
                    $stack[++$level] = &$stack[$oldLevel][$field];
                    $field = '';
                    break;
                case ')':
                    if ('' != $field) {
                        $stack[$level][$field] = true;
                    }
                    unset($stack[$level--]);
                    if ($level < 0) {
                        throw new WrongFields('Bad _fields syntax');
                    }
                    $field = '';
                    break;
                default:
                    $field.= $char;
                    if ($i == $len && '' !== $field) {
                        $stack[$level][$field] = true;
                        $field = '';
                    }
            }
        }
        if (count($stack) > 1) {
            throw new WrongFields('Bad _fields syntax');
        }

        return $fields;
    }
}
