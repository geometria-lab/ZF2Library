<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 10.08.12
 * Time: 18:18
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Exception;

class Exception extends \Exception
{
    protected $errorCode;
    protected $errorMessage;
    protected $httpCode;

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}