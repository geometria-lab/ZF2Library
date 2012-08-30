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
class ResourceNotFound extends Exception
{
    /**
     * @var int
     */
    protected $errorCode = 46;
    /**
     * @var string
     */
    protected $errorMessage = 'Resource not found';
    /**
     * @var int
     */
    protected $httpCode = 404;
}