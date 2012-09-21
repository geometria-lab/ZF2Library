<?php

namespace GeometriaLab\Api\Mvc\View\Http;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface as ZendEvents;
use Zend\Mvc\MvcEvent as ZendMvcEvent;

use GeometriaLab\Model,
    GeometriaLab\Model\ModelInterface;

use GeometriaLab\Api\Mvc\View\Model\ApiModel,
    GeometriaLab\Api\Exception\WrongFieldsException;

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
            $errorCode    = $apiException->getCode();
            $errorMessage = $apiException->getMessage();
        } else {
            $errorCode    = null;
            $errorMessage = null;
        }

        $apiModel->setVariable(ApiModel::FIELD_ERRORCODE,    $errorCode);
        $apiModel->setVariable(ApiModel::FIELD_ERRORMESSAGE, $errorMessage);

        if ($apiException) {
            $apiModel->setVariable(ApiModel::FIELD_DATA, $apiException->getData());
        }

        $e->setResult($apiModel);
    }

    /**
     * @param ZendMvcEvent $e
     * @throws WrongFieldsException
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
            /* @var \GeometriaLab\Api\Stdlib\Extractor\Service $extractor */
            $extractor = $e->getApplication()->getServiceManager()->get('Extractor');
            $extractedData = $extractor->extract($data, $fieldsData);
            $wrongProperties = $extractor->getWrongFields();

            if (!empty($wrongProperties)) {
                $exception = new WrongFieldsException('Wrong fields provided');
                $exception->setData($wrongProperties);
                throw $exception;
            }

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
     * @throws WrongFieldsException
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
                        // @TODO Need all messages with name
                        throw new WrongFieldsException('Bad _fields syntax');
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
            throw new WrongFieldsException('Bad _fields syntax');
        }

        return $fields;
    }
}
