<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 10.08.12
 * Time: 18:18
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Exception;

class ValidationError extends Exception
{
    protected $errorCode = 42;
    protected $errorMessage = 'Validation error';
    protected $httpCode = 400;
}