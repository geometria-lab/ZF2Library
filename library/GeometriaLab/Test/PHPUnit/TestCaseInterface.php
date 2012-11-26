<?php

namespace GeometriaLab\Test\PHPUnit;

use GeometriaLab\Test\PHPUnit\Plugin\PluginManager;

interface TestCaseInterface
{
    /**
     * @param PluginManager $pluginManager
     * @return TestCaseInterface
     */
    public function setPluginManager(PluginManager $pluginManager);

    /**
     * @return \GeometriaLab\Test\PHPUnit\Plugin\PluginManager
     */
    public function getPluginManager();

    /**
     * Get plugin instance
     *
     * @param  string     $name    Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return mixed
     */
    public function plugin($name, array $options = null);

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
    public function __call($method, $params);
}
