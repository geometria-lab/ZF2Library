<?php

namespace GeometriaLab\Mongo;

class Manager
{
    /**
     * @var Manager
     */
    static protected $instance;

    /**
     * MongoDb instances
     *
     * @var \MongoDB[]
     */
    protected $mongoDbInstatnces = array();

    /**
     * Get instance
     *
     * @static
     * @return Manager
     */
    static public function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Set MongoDB instance
     *
     * @param $name
     * @param \MongoDB $mongoDb
     * @return Manager
     */
    public function set($name, \MongoDB $mongoDb)
    {
        $this->mongoDbInstatnces[$name] = $mongoDb;

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

        return $this->mongoDbInstatnces[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->mongoDbInstatnces[$name]);
    }

    /**
     * @param $name
     * @return Manager
     * @throws \InvalidArgumentException
     */
    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException("Instance '$name' is not present");
        }

        unset($this->mongoDbInstatnces[$name]);

        return $this;
    }

    /**
     * @return Manager
     */
    public function removeAll()
    {
        $this->mongoDbInstatnces = array();

        return $this;
    }
}