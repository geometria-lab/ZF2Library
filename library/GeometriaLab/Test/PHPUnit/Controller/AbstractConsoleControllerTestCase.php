<?php

namespace GeometriaLab\Test\PHPUnit\Controller;

/**
 * @method \Zend\Console\Request getRequest()
 * @method \Zend\Console\Response getResponse()
 */
abstract class AbstractConsoleControllerTestCase extends AbstractControllerTestCase
{
    protected $useConsoleRequest = true;
}
