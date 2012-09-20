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
class ResourceNotFoundException extends AbstractException
{
    /**
     * @var int
     */
    protected $code = 46;
    /**
     * @var string
     */
    protected $message = 'Resource not found';
    /**
     * @var int
     */
    protected $httpCode = 404;
}