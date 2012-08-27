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
class UnauthenticatedUser extends Exception
{
    /**
     * @var int
     */
    protected $errorCode = 45;
    /**
     * @var string
     */
    protected $errorMessage = 'Unauthenticated user';
    /**
     * @var int
     */
    protected $httpCode = 403;
}