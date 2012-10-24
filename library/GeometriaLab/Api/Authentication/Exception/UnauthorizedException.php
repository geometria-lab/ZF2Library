<?php

namespace GeometriaLab\Api\Authentication\Exception;

class UnauthorizedException extends \GeometriaLab\Api\Exception\AbstractException
{
    /**
     * @var int
     */
    protected $code = 48;
    /**
     * @var string
     */
    protected $message = 'Unauthorized user';
    /**
     * @var int
     */
    protected $httpCode = 401;
    /**
     * @var array
     */
    protected $data = array('error' => 'invalid_grant');
}