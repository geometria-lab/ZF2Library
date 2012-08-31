<?php

namespace GeometriaLab\Mongo;

use Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface;

class ServiceFactory implements ZendFactoryInterface
{
    /**
     * @var ServiceFactory
     */
    static protected $instance;

    /**
     * @var array
     */
    private $config = array();

    /**
     * Mongo instances
     *
     * @var \Mongo[]
     */
    protected $mongoInstances = array();

    /**
     * MongoDb instances
     *
     * @var \MongoDb[]
     */
    protected $mongoDbInstances = array();

    /**
     * Get instance
     *
     * @static
     * @return ServiceFactory
     */
    static public function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return ServiceFactory
     * @throws \InvalidArgumentException
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');
        if (!isset($config['mongo'])) {
            throw new \InvalidArgumentException('Need "mongo" param in config');
        }

        self::getInstance()->setConfig($config['mongo']);

        return self::getInstance();
    }

    /**
     * @param $config
     * @return ServiceFactory
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Get all Mongo instances
     *
     * @return \Mongo[]
     */
    public function getAll()
    {
        return $this->mongoDbInstances;
    }

    /**
     * Get MongoDB instance
     *
     * @param $name
     * @return \MongoDB
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException("Instance '$name' is not present");
        }

        return $this->mongoDbInstances[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        if (!isset($this->mongoDbInstances[$name])) {
            return $this->create($name);
        }
        return isset($this->mongoDbInstances[$name]);
    }

    /**
     * Create Mongo instance
     *
     * @param $name
     * @return \Mongo
     * @throws \InvalidArgumentException
     */
    public function create($name)
    {
        if (!isset($this->config[$name]['connectionString'])) {
            throw new \InvalidArgumentException('Need "connectionString" param in config');
        }

        if (!isset($this->config[$name]['db'])) {
            throw new \InvalidArgumentException('Need "db" param in config');
        }

        if (!isset($this->mongoInstances[$this->config[$name]['connectionString']])) {
            $this->mongoInstances[$this->config[$name]['connectionString']] = new \Mongo($this->config[$name]['connectionString']);
        }

        $this->mongoDbInstances[$name] = $this->mongoInstances[$this->config[$name]['connectionString']]->selectDB($this->config[$name]['db']);

        return $this->mongoDbInstances[$name];
    }

    /**
     * Remove Mongo instance
     *
     * @param $name
     * @return ServiceFactory
     * @throws \InvalidArgumentException
     */
    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException("Instance '$name' is not present");
        }

        unset($this->mongoInstances[$name]);

        return $this;
    }

    /**
     * @return ServiceFactory
     */
    public function removeAll()
    {
        $this->mongoInstances = array();

        return $this;
    }
}