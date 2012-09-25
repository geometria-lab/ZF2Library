<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 06.08.12
 * Time: 12:42
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Mvc\View\Model;

use Zend\View\Model\ViewModel as ZendViewModel;

class ApiModel extends ZendViewModel
{
    const FIELD_DATA = 'data';
    const FIELD_HTTPCODE = 'httpCode';
    const FIELD_STATUS = 'status';
    const FIELD_ERRORCODE = 'errorCode';
    const FIELD_ERRORMESSAGE = 'errorMessage';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const STATUS_ERROR = 'error';

    protected $terminate = true;
}