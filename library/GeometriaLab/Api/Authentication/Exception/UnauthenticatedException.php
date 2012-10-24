<?php

namespace GeometriaLab\Api\Authentication\Exception;

class UnauthenticatedException extends \GeometriaLab\Api\Exception\AbstractException
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
    /**
     * @var array
     */
    protected $data = array('error' => 'invalid_scope');
}