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
class WrongFields extends AbstractException
{
    /**
     * @var int
     */
    protected $code = 41;
    /**
     * @var string
     */
    protected $message = 'Wrong fields';
    /**
     * @var int
     */
    protected $httpCode = 400;
}