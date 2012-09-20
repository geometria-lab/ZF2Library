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
    protected $message = 'Validation error';
    /**
     * @var int
     */
    protected $httpCode = 400;

    /**
     * @var Params
     */
    protected $params;

    /**
     * @param $params
     */
    public function setParams(Params $params)
    {
        $this->params = $params;
    }

    /**
     * @return Params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return mixed|void
     */
    public function getData()
    {
        $result = array();

        foreach ($this->getParams()->getErrorMessages() as $fieldName => $errors) {
            foreach ($errors as $type => $message) {
                $result[] = array(
                    'field' => $fieldName,
                    'type' => $type,
                    'message' => $message,
                );
            }
        }

        return $result;
    }
}