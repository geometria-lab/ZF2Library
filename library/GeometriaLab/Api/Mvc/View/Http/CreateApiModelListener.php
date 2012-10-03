<?php

namespace GeometriaLab\Api\Mvc\View\Http;

use GeometriaLab\Model\ModelInterface,
    GeometriaLab\Model\CollectionInterface,
    GeometriaLab\Model\Schema\SchemaInterface,
    GeometriaLab\Api\Mvc\View\Model\ApiModel,
    GeometriaLab\Api\Paginator\ModelPaginator,
    GeometriaLab\Api\Exception\InvalidFieldsException,
    GeometriaLab\Api\Mvc\Controller\Action\Params\AbstractParams,
    GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\Property\IntegerProperty as ParamsIntegerProperty;

use Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\Mvc\View\Http\InjectViewModelListener as ZendInjectViewModelListener,
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
            // Set params to paginator
            if ($data instanceof ModelPaginator) {
                $params = $e->getRouteMatch()->getParam('params');
                $this->populatePaginatorFromParams($data, $params);
            }

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

    /**
     * @param ModelPaginator $paginator
     * @param AbstractParams $params
     * @throws \RuntimeException
     */
    protected function populatePaginatorFromParams(ModelPaginator $paginator, AbstractParams $params)
    {
        $paramsSchema = $params::getSchema();

        $this->checkLimitProperty($paramsSchema);
        $this->checkOffsetProperty($paramsSchema);

        $paginator->setLimit($params->get('limit'));
        $paginator->setOffset($params->get('offset'));
    }

    /**
     * Check limit property
     *
     * @param SchemaInterface $paramsSchema
     * @throws \RuntimeException
     */
    protected function checkLimitProperty(SchemaInterface $paramsSchema)
    {
        if (!$paramsSchema->hasProperty('limit')) {
            throw new \RuntimeException('Limit must be present in params');
        }

        $limitProperty = $paramsSchema->getProperty('limit');

        if (!$limitProperty instanceof ParamsIntegerProperty) {
            throw new \RuntimeException('Limit property must be integer');
        }

        if ($limitProperty->getDefaultValue() === null) {
            throw new \RuntimeException('Limit property must have default value');
        }

        $max = 0;
        $min = 0;
        $hasLessThanValidator = false;
        $hasGreaterThanValidator = false;
        $validators = $limitProperty->getValidatorChain()->getValidators();

        foreach ($validators as $validator) {
            $validatorInstance = $validator['instance'];
            if ($validatorInstance instanceof ZendLessThanValidator) {
                /* @var ZendLessThanValidator $validatorInstance */
                $hasLessThanValidator = true;
                $max = $validatorInstance->getMax();

                if (!$validatorInstance->getInclusive()) {
                    $max--;
                }
            } elseif ($validatorInstance instanceof ZendGreaterThanValidator) {
                /* @var ZendGreaterThanValidator $validatorInstance */
                $hasGreaterThanValidator = true;
                $min = $validatorInstance->getMin();

                if (!$validatorInstance->getInclusive()) {
                    $min++;
                }
            }
        }

        if (!$hasLessThanValidator) {
            throw new \RuntimeException("Limit property must have 'LessThan' validator");
        }

        if (!$hasGreaterThanValidator) {
            throw new \RuntimeException("Limit property must have 'GreaterThan' validator");
        }

        if ($limitProperty->getDefaultValue() < 1 || $min < 1) {
            throw new \RuntimeException("Limit property must be greater or equal then 1");
        }

        if ($limitProperty->getDefaultValue() > 100 || $max > 100) {
            throw new \RuntimeException("Limit property must be less or equal then 100");
        }
    }

    /**
     * Check offset property
     *
     * @param SchemaInterface $paramsSchema
     * @throws \RuntimeException
     */
    protected function checkOffsetProperty(SchemaInterface $paramsSchema)
    {
        if (!$paramsSchema->hasProperty('offset')) {
            throw new \RuntimeException('Offset must be present in params');
        }

        if (!$paramsSchema->getProperty('offset') instanceof ParamsIntegerProperty) {
            throw new \RuntimeException('Offset must be integer');
        }
    }
}
