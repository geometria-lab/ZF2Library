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
class UnconfirmedUser extends Exception
{
    /**
     * @var int
     */
    protected $errorCode = 43;
    /**
     * @var string
     */
    protected $errorMessage = 'Unconfirmed user';
    /**
     * @var int
     */
    protected $httpCode = 401;
}