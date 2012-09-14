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
abstract class AbstractException extends \Exception
{
    /**
     * @var integer
     */
    protected $httpCode;

    /**
     * @return integer
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }
}