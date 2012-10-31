<?php

namespace GeometriaLab\Api\Exception;

/**
 *
 */
class BadRequestException extends AbstractException
{
    /**
     * @var int
     */
    protected $code = 48;
    /**
     * @var string
     */
    protected $message = 'Bad request';
    /**
     * @var int
     */
    protected $httpCode = 400;
}