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
}
