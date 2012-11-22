<?php

namespace GeometriaLab\Test\PHPUnit\Controller;

use GeometriaLab\Test\Helper\HelperBroker;

/**
 * @method \Zend\Console\Request getRequest()
 * @method \Zend\Console\Response getResponse()
 */
abstract class AbstractConsoleControllerTestCase extends AbstractControllerTestCase
{
    protected $useConsoleRequest = true;
}
