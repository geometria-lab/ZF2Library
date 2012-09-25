<?php

namespace GeometriaLab\Api\Exception;

use GeometriaLab\Api\Mvc\Controller\Action\Params\Params;

/**
 *
 */
class InvalidParamsException extends AbstractException
{
    /**
     * @var int
     */
    protected $code = 42;
    /**
     * @var string
     */
    protected $message = 'Invalid params';
    /**
     * @var int
     */
    protected $httpCode = 400;

    /**
     * @var Params
     */
    protected $params;

    /**
     * @return mixed
     */
    public function getData()
    {
        $data = parent::getData();
        if ($data !== null) {
            return $data;
        }

        $result = array();
        $params = $this->getParams();

        if ($params !== null) {
            foreach ($params->getErrorMessages() as $fieldName => $errors) {
                foreach ($errors as $type => $message) {
                    $result[] = array(
                        'field' => $fieldName,
                        'type' => $type,
                        'message' => $message,
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Set params object
     *
     * @param Params $params
     * @return InvalidParamsException
     */
    public function setParams(Params $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Get params object
     *
     * @return Params|null
     */
    public function getParams()
    {
        return $this->params;
    }
}