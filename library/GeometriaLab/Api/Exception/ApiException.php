<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 10.08.12
 * Time: 18:18
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Mvc\Exception;

class Exception extends \Exception
{
    protected $apiCode;
    protected $httpStatusCode;

    public function getApiCode()
    {
        return $this->apiCode;
    }

    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }
}