<?php

namespace GeometriaLab\Api\Exception;

class ObjectNotFoundException extends AbstractException
{
    /**
     * @var int
     */
    protected $code = 47;
    /**
     * @var string
     */
    protected $message = 'Object not found';
    /**
     * @var int
     */
    protected $httpCode = 404;
}