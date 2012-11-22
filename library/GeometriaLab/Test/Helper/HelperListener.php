<?php

namespace GeometriaLab\Test\Helper;

use GeometriaLab\Test\TestCaseInterface;

class HelperListener implements \PHPUnit_Framework_TestListener
{
    /**
     * @var HelperBroker
     */
    protected $helperBroker;

    /**
     * @param array $helpers Helpers map
     */
    public function __construct(array $helpers = array())
    {
        $this->helperBroker = new HelperBroker($helpers);
    }

    /**
     * An error occurred.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  \Exception              $e
     * @param  float                   $time
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {

    }

    /**
     * A failure occurred.
     *
     * @param  \PHPUnit_Framework_Test                 $test
     * @param  \PHPUnit_Framework_AssertionFailedError $e
     * @param  float                                   $time
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {

    }

    /**
     * Incomplete test.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  \Exception              $e
     * @param  float                   $time
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {

    }

    /**
     * Skipped test.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  \Exception              $e
     * @param  float                   $time
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {

    }

    /**
     * A test suite started.
     *
     * @param  \PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {

    }

    /**
     * A test suite ended.
     *
     * @param  \PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {

    }

    /**
     * A test started.
     *
     * @param  \PHPUnit_Framework_Test $test
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        if ($test instanceof TestCaseInterface) {
            $this->helperBroker->setTest($test);
            $test->setHelperBroker($this->helperBroker);
        }
    }

    /**
     * A test ended.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  float                   $time
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {

    }
}