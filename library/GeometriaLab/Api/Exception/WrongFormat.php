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
class WrongFormat extends Exception
{
    /**
     * @var int
     */
    protected $errorCode = 40;
    /**
     * @var string
     */
    protected $errorMessage = 'Wrong format';
    /**
     * @var int
     */
    protected $httpCode = 400;
}