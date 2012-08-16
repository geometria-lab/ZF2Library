<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 10.08.12
 * Time: 18:18
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Exception;

class UnconfirmedUser extends Exception
{
    protected $errorCode = 43;
    protected $errorMessage = 'Unconfirmed user';
    protected $httpCode = 401;
}