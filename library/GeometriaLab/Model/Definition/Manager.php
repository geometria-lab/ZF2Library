<?php

namespace GeometriaLab\Model\Definition;

use GeometriaLab\Model\Definition;

class Manager
{
    /**
     * Instance
     *
     * @var Manager
     */
    static protected $instance;

    /**
     * Definitions
     *
     * @var array
     */
    protected $definitions = array();

    /**
     * Get instance
     *
     * @static
     * @return Manager
     */
    static public function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Add model
     *
     * @param string $modelClass
     * @return Definition
     * @throws \Exception
     */
    public function define($modelClass)
    {
        if ($this->has($modelClass)) {
            throw new \Exception("Model '$modelClass' already defined");
        }

        return $this->definitions[$modelClass] = new Definition($modelClass);
    }

    /**
     * Get model definition
     *
     * @param string $modelClass
     * @return Definition
     * @throws \Exception
     */
    public function get($modelClass)
    {
        if (!$this->has($modelClass)) {
            throw new \Exception("Model '$modelClass' not defined");
        }

        return $this->definitions[$modelClass];
    }

    /**
     * Has model definition?
     *
     * @param string $modelClass
     * @return bool
     */
    public function has($modelClass)
    {
        return isset($this->definitions[$modelClass]);
    }
}