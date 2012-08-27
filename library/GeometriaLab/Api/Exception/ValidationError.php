<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 10.08.12
 * Time: 18:18
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Exception;

/**
 *
 */
class ValidationError extends Exception
{
    /**
     * @var int
     */
    protected $errorCode = 42;
    /**
     * @var string
     */
    protected $errorMessage = 'Validation error';
    /**
     * @var int
     */
    protected $httpCode = 400;
}