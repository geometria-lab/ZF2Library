<?php

namespace GeometriaLab\Test\PHPUnit\Plugin;

use GeometriaLab\Test\PHPUnit\TestCaseInterface;

use Zend\ServiceManager\Config as ZendConfig;

class PluginListener implements \PHPUnit_Framework_TestListener
{
    /**
     * @var PluginManager
     */
    protected $pluginManager;

    /**
     * @param array $plugins Plugins map
     */
    public function __construct(array $plugins = array())
    {
        $this->pluginManager = new PluginManager(new ZendConfig(array(
            'invokables' => $plugins,
        )));
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
            $test->setPluginManager($this->pluginManager);
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
        if ($test instanceof TestCaseInterface) {
            foreach ($test->getPluginManager()->getRegisteredServices() as $serviceName => $plugins) {
                if ($serviceName === 'instances') {
                    continue;
                }
                foreach ($plugins as $pluginName) {
                    $plugin = $test->getPluginManager()->get($pluginName);
                    if (method_exists($plugin, 'tearDown')) {
                        $plugin->tearDown();
                    }
                }
            }
        }
    }
}