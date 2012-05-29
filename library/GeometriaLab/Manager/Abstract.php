<?php
/**
 * @author munkie
 */
abstract class Geometria_Manager_Abstract
{
    /**
     * Configs
     *
     * instanceName => mongo config
     *
     * @var array
     */
    protected $_configs = array();

    /**
     *
     * @var array
     */
    static protected $_instances = array();

    /**
     * Constructor is protected - Singleton
     */
    final private function __construct()
    {
    }

    /**
     * @return Geometria_Manager_Abstract
     */
    static public function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new static;
        }
        return self::$_instances[$class];
    }

    /**
     * Clear singleton instance
     *
     * Mainly for unit tests
     */
    static public function clearInstance()
    {
        $class = get_called_class();
        if (isset(self::$_instances[$class])) {
            unset(self::$_instances[$class]);
        }
    }

    /**
     * @static
     */
    static public function clearAllInstances()
    {
        self::$_instances = array();
    }

    /**
     * @throws Geometria_Mongo_Exception
     */
    public function __clone()
    {
        throw new Geometria_Manager_Exception('Cloning of ' . __CLASS__ . ' is forbidden. It is a singleton');
    }

    /**
     *
     * @param string $name
     * @param array $config
     *
     * @return Geometria_Manager_Abstract
     */
    public function addConfig($name, array $config)
    {
        $this->_configs[$name] = $config;
        return $this;
    }

    /**
     *
     * @param array $configs
     *
     * @return Geometria_Manager_Abstract
     */
    public function addConfigs(array $configs)
    {
        foreach ($configs as $name => $options) {
            $this->addConfig($name, $options);
        }
        return $this;
    }

    /**
     *
     * @param array $configs
     *
     * @return Geometria_Manager_Abstract
     */
    public function setConfigs(array $configs)
    {
        $this->clearConfigs();
        $this->addConfigs($configs);
        return $this;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->_configs;
    }

    /**
     *
     * @param string $name
     * @return array
     * @throws Geometria_Mongo_Exception
     */
    public function getConfig($name)
    {
        if (isset($this->_configs[$name])) {
            return $this->_configs[$name];
        }
        throw new Geometria_Manager_Exception("Config $name not found in manager");
    }

    /**
     * @return Geometria_Manager_Abstract
     */
    public function clearConfigs()
    {
        $this->_configs = array();
        return $this;
    }
}