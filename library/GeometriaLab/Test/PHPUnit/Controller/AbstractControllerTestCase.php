<?php

namespace GeometriaLab\Test\PHPUnit\Controller;

use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase as ZendAbstractControllerTestCase;

use GeometriaLab\Test\TestCaseInterface,
    GeometriaLab\Test\Plugin\PluginManager;

abstract class AbstractControllerTestCase extends ZendAbstractControllerTestCase implements TestCaseInterface
{
    /**
     * @var PluginManager
     */
    protected $plugins;

    public function setUp()
    {
        $this->setApplicationConfig(
            include 'config/application.config.php'// Use relative path
        );

        // @TODO Hack
        $this->getApplication();

        parent::setUp();
    }

    /**
     * @param PluginManager $pluginManager
     * @return AbstractHttpControllerTestCase
     */
    public function setPluginManager(PluginManager $pluginManager)
    {
        $this->plugins = $pluginManager;
        $this->plugins->setTest($this);

        return $this;
    }

    /**
     * @return PluginManager
     */
    public function getPluginManager()
    {
        if (!$this->plugins) {
            $this->setPluginManager(new PluginManager());
        }

        return $this->plugins;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $name    Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return mixed
     */
    public function plugin($name, array $options = null)
    {
        return $this->getPluginManager()->get($name, $options);
    }

    /**
     * Method overloading: return/call plugins
     *
     * If the plugin is a functor, call it, passing the parameters provided.
     * Otherwise, return the plugin instance.
     *
     * @param  string $method
     * @param  array  $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        $plugin = $this->plugin($method);
        if (is_callable($plugin)) {
            return call_user_func_array($plugin, $params);
        }

        return $plugin;
    }

    /**
     * Assert that JSON piece by path $path and $expected are equals
     *
     * @param string $path
     * @param mixed $expected
     * @param string $message
     * @param int $delta
     * @param int $maxDepth
     * @param bool $canonicalize
     * @param bool $ignoreCase
     */
    public function assertJsonEquals($path, $expected, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        $message = ($message) ?: sprintf('Failed asserting that JSON path "%s" value equals "%s"', $path, $expected);

        $this->assertContentType('application/json');

        $actual = $this->getJsonValueByPath($path);

        self::assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     *  Assert that content-type header and $expected are equals
     *
     * @param string $expected
     * @param string $message
     * @param int $delta
     * @param int $maxDepth
     * @param bool $canonicalize
     * @param bool $ignoreCase
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    protected function assertContentType($expected, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        $contentType = $this->getResponse()->getHeaders()->get('content-type');

        if ($contentType === false) {
            throw new \PHPUnit_Framework_AssertionFailedError('Content-type not found in Headers');
        }

        self::assertEquals($expected, $contentType->getFieldValue(), $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * Get JSON value by path
     *
     * @param string $path
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @return mixed
     */
    protected function getJsonValueByPath($path)
    {
        $path = trim($path, '/');
        $sections = explode('/', $path);

        $json = $this->getJsonFromResponse();

        if (!is_array($json)) {
            throw new \PHPUnit_Framework_AssertionFailedError('Path not found in JSON');
        }

        foreach ($sections as $section) {
            if (array_key_exists($section, $json)) {
                $json = $json[$section];
            } else {
                throw new \PHPUnit_Framework_AssertionFailedError('Path not found in JSON');
            }
        }

        return $json;
    }

    /**
     * Get JSON from Response
     *
     * @return array
     */
    protected function getJsonFromResponse()
    {
        return json_decode($this->getResponse()->getContent(), true);
    }
}
