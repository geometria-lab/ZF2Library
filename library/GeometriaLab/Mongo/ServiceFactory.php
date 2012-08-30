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
     * MongoDb instances
     *
     * @var \MongoDB[]
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
            throw new \InvalidArgumentException('Invalid ');
        }
        $this->setConfig($config['mongo']);

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
     * Set MongoDB instance
     *
     * @param $name
     * @param \MongoDB $mongoDb
     * @return ServiceFactory
     */
    public function set($name, \MongoDB $mongoDb)
    {
        $this->mongoDbInstances[$name] = $mongoDb;

        return $this;
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
            if (isset($this->config[$name])) {
                $mongo = new \Mongo($this->config[$name]['connectionString']);
                $mongoDb = $mongo->selectDB($this->config[$name]['db']);
                $this->set($name, $mongoDb);
            }
        }
        return isset($this->mongoDbInstances[$name]);
    }

    /**
     * @param $name
     * @return ServiceFactory
     * @throws \InvalidArgumentException
     */
    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException("Instance '$name' is not present");
        }

        unset($this->mongoDbInstances[$name]);

        return $this;
    }

    /**
     * @return ServiceFactory
     */
    public function removeAll()
    {
        $this->mongoDbInstances = array();

        return $this;
    }
}