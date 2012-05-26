<?php

namespace GeometriaLab\Model\Definition;

use GeometriaLab\Model\Definition;

class Manager implements \IteratorAggregate
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
     * Add model definition
     *
     * @param DefinitionInterface $definition
     * @return DefinitionInterface
     * @throws \Exception
     */
    public function add(DefinitionInterface $definition)
    {
        $className = $definition->getClassName();

        $className = $this->filterClassName($className);

        if ($this->has($className)) {
            throw new \Exception("Model '{$className}' already defined");
        }

        return $this->definitions[$className] = $definition;
    }

    /**
     * Get model definition
     *
     * @param string $modelClass
     * @return DefinitionInterface
     * @throws \Exception
     */
    public function get($modelClass)
    {
        $modelClass = $this->filterClassName($modelClass);

        if (!$this->has($modelClass)) {
            throw new \Exception("Model '$modelClass' not defined");
        }

        return $this->definitions[$modelClass];
    }

    /**
     * Get all definition
     *
     * @return array
     */
    public function getAll()
    {
        return $this->definitions;
    }

    /**
     * Has model definition?
     *
     * @param string $modelClass
     * @return bool
     */
    public function has($modelClass)
    {
        $modelClass = $this->filterClassName($modelClass);

        return isset($this->definitions[$modelClass]);
    }

    /**
     * Iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getAll());
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