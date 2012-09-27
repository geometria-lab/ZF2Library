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
class InvalidFormatException extends AbstractException
{
    /**
     * @var int
     */
    protected $code = 40;
    /**
     * @var string
     */
    protected $message = 'Invalid format';
    /**
     * @var int
     */
    protected $httpCode = 400;
}