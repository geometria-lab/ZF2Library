<?php

namespace GeometriaLab\Model\Schema;

use GeometriaLab\Model\Schema\Schema;

class Manager implements \IteratorAggregate
{
    /**
     * Instance
     *
     * @var Manager
     */
    static protected $instance;

    /**
     * Schemas
     *
     * @var Schema[]
     */
    protected $schemas = array();

    /**
     * Constructor is protected - Singleton
     */
    final private function __construct()
    {

    }

    /**
     * Get manager instance
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
     * @throws \RuntimeException
     */
    public function __clone()
    {
        throw new \RuntimeException('Cloning of ' . __CLASS__ . ' is forbidden. It is a singleton');
    }

    /**
     * Add model schema
     *
     * @param Schema $schema
     * @return Manager
     * @throws \InvalidArgumentException
     */
    public function add(Schema $schema)
    {
        $className = $schema->getClassName();

        $className = $this->filterClassName($className);

        if ($this->has($className)) {
            throw new \InvalidArgumentException("Model '{$className}' schema already added");
        }

        return $this->schemas[$className] = $schema;
    }

    /**
     * Get model schema
     *
     * @param string $modelClass
     * @return Schema
     * @throws \InvalidArgumentException
     */
    public function get($modelClass)
    {
        $modelClass = $this->filterClassName($modelClass);

        if (!$this->has($modelClass)) {
            throw new \InvalidArgumentException("Model '$modelClass' schema not present");
        }

        return $this->schemas[$modelClass];
    }

    /**
     * Get all schemas
     *
     * @return Schema[]
     */
    public function getAll()
    {
        return $this->schemas;
    }

    /**
     * Has model schema?
     *
     * @param string $modelClass
     * @return bool
     */
    public function has($modelClass)
    {
        $modelClass = $this->filterClassName($modelClass);

        return isset($this->schemas[$modelClass]);
    }

    /**
     * Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getAll());
    }

    /**
     * Remove schema
     *
     * @param $modelClass
     * @return Manager
     * @throws \InvalidArgumentException
     */
    public function remove($modelClass)
    {
        if (!$this->has($modelClass)) {
            throw new \InvalidArgumentException("Model '$modelClass' schema not present");
        }

        unset($this->schemas[$modelClass]);

        return $this;
    }

    /**
     * Remove all schema
     *
     * @return Manager
     */
    public function removeAll()
    {
        $this->schemas = array();

        return $this;
    }

    /**
     * @param $className
     * @return string
     */
    protected function filterClassName($className)
    {
        if (strpos($className, '\\') === 0) {
            $className = substr($className, 1);
        }

        return $className;
    }
}