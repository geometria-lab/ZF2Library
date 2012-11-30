<?php

namespace GeometriaLab\Api\Mvc\View\Http;

use GeometriaLab\Model\ModelInterface,
    GeometriaLab\Model\CollectionInterface,
    GeometriaLab\Api\Mvc\View\Model\ApiModel,
    GeometriaLab\Api\Paginator\ModelPaginator,
    GeometriaLab\Api\Exception\InvalidFieldsException;

use Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\Mvc\View\Http\InjectViewModelListener as ZendInjectViewModelListener,
    Zend\Http\PhpEnvironment\Response,
    Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface,
    Zend\EventManager\EventManagerInterface as ZendEvents,
    Zend\Validator\LessThan as ZendLessThanValidator,
    Zend\Validator\GreaterThan as ZendGreaterThanValidator;

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
        $this->listeners[] = $events->attach(ZendMvcEvent::EVENT_DISPATCH_ERROR, array($this, 'createApiModel'), -99);

        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', ZendMvcEvent::EVENT_DISPATCH, array($this, 'createApiModel'), 1);
        $this->listeners[] = $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', ZendMvcEvent::EVENT_DISPATCH, array($this, 'extractData'), 0);
        $this->listeners[] = $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', ZendMvcEvent::EVENT_DISPATCH, array(new ZendInjectViewModelListener(), 'injectViewModel'), -100);
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

        /* @var Response $response */
        $response = $e->getResponse();
        $apiException = $e->getParam('apiException', false);

        if ($response->getStatusCode() === Response::STATUS_CODE_200) {
            if ($apiModel->getVariable(ApiModel::FIELD_DATA) === null) {
                $response->setStatusCode(Response::STATUS_CODE_204);
            }
            if ($e->getRouteMatch()->getParam('action') === 'create') {
                $response->setStatusCode(Response::STATUS_CODE_201);
            }
        }

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
     * Extract data
     *
     * @param ZendMvcEvent $e
     * @throws \RuntimeException
     * @throws InvalidFieldsException
     */
    public function extractData(ZendMvcEvent $e)
    {
        $result = $e->getResult();

        if (!$result instanceof ApiModel) {
            throw new \RuntimeException('Result must be ApiModel');
        }

        $data = $result->getVariable(ApiModel::FIELD_DATA);

        if ($this->isDataPaginatorOrModelOrCollection($data)) {
            $fields = $e->getRequest()->getQuery()->get('_fields');
            $fieldsData = self::createFieldsFromString($fields);

            /* @var \GeometriaLab\Api\Stdlib\Extractor\Service $extractor */
            $extractor = $e->getApplication()->getServiceManager()->get('Extractor');
            $extractedData = $extractor->extract($data, $fieldsData);

            $wrongProperties = $extractor->getInvalidFields();

            if (!empty($wrongProperties)) {
                $exception = new InvalidFieldsException('Invalid fields');
                $exception->setFields($wrongProperties);
                throw $exception;
            }

            $result->setVariable(ApiModel::FIELD_DATA, $extractedData);
        }
    }

    /**
     * Create Fields from string
     *
     * @static
     * @param $fieldsString
     * @return array
     * @throws InvalidFieldsException
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
                        throw new InvalidFieldsException('Bad _fields syntax');
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
            throw new InvalidFieldsException('Bad _fields syntax');
        }

        return $fields;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    protected function isDataPaginatorOrModelOrCollection($data)
    {
        return $data instanceof ModelPaginator ||
               $data instanceof ModelInterface ||
               $data instanceof CollectionInterface;
    }
}
