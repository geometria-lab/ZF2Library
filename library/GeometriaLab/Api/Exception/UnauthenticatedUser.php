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
 * @todo Move it to Application
 */
class UnauthenticatedUser extends AbstractException
{
    /**
     * @var int
     */
    protected $code = 45;
    /**
     * @var string
     */
    protected $message = 'Unauthenticated user';
    /**
     * @var int
     */
    protected $httpCode = 403;
}