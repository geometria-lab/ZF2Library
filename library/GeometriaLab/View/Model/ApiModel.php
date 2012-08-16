<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 06.08.12
 * Time: 12:42
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\View\Model;

class ApiModel extends \Zend\View\Model\ViewModel
{
    const FIELD_DATA = 'data';
    const FIELD_CODE = 'code';
    const FIELD_STATUS = 'status';
    const FIELD_MESSAGE = 'message';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const STATUS_ERROR = 'error';

    protected $terminate = true;
}