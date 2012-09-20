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
class AccessDeniedException extends AbstractException
{
    /**
     * @var int
     */
    protected $code = 44;
    /**
     * @var string
     */
    protected $message = 'Access denied';
    /**
     * @var int
     */
    protected $httpCode = 401;
}