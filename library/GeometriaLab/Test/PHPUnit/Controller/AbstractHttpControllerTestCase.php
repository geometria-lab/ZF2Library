<?php

namespace GeometriaLab\Test\PHPUnit\Controller;

/**
 * @method \Zend\Http\PhpEnvironment\Request getRequest()
 * @method \Zend\Http\PhpEnvironment\Response getResponse()
 */
abstract class AbstractHttpControllerTestCase extends AbstractControllerTestCase
{
    protected $useConsoleRequest = false;
}
