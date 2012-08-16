<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 10.08.12
 * Time: 18:18
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Mvc\Exception;

class WrongFields extends Exception
{
    protected $errorCode = 41;
    protected $errorMessage = 'Wrong fields';
    protected $httpCode = 400;
}