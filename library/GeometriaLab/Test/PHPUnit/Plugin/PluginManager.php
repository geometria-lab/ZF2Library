<?php

namespace GeometriaLab\Test\PHPUnit\Plugin;

use GeometriaLab\Test\PHPUnit\TestCaseInterface;

use Zend\ServiceManager\AbstractPluginManager as ZendAbstractPluginManager,
    Zend\ServiceManager\ConfigInterface as ZendConfigInterface;

class PluginManager extends ZendAbstractPluginManager
{
    /**
     * @var TestCaseInterface
     */
    protected $test;

    /**
     * Constructor
     *
     * After invoking parent constructor, add an initializer to inject the
     * attached test, if any, to the currently requested plugin.
     *
     * @param  null|ZendConfigInterface $configuration
     */
    public function __construct(ZendConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->addInitializer(array($this, 'injectTest'));
    }

    /**
     * Retrieve a registered instance
     *
     * @param  string $name
     * @param  mixed  $options
     * @param  bool   $usePeeringServiceManagers
     * @return mixed
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        $plugin = parent::get($name, $options, $usePeeringServiceManagers);
        $this->injectTest($plugin);

        return $plugin;
    }

    /**
     * Set test object
     *
     * @param TestCaseInterface $test
     * @return PluginManager
     */
    public function setTest(TestCaseInterface $test)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Get test object
     *
     * @return TestCaseInterface
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Inject a plugin instance with the registered test
     *
     * @param  object $plugin
     * @return void
     */
    public function injectTest($plugin)
    {
        if (!is_object($plugin)) {
            return;
        }
        if (!method_exists($plugin, 'setTest')) {
            return;
        }

        $test = $this->getTest();

        $plugin->setTest($test);
    }

    /**
     * Validate the plugin
     *
     * Any plugin is considered valid in this context.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidPluginException
     */
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof PluginInterface) {
            throw new Exception\InvalidPluginException(sprintf(
                'Plugin of type %s is invalid; must implement %s\Plugin\PluginInterface',
                (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
                __NAMESPACE__
            ));
        }
    }
}
