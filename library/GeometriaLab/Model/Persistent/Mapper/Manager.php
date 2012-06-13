<?php

namespace GeometriaLab\Model\Persistent\Mapper;

class Manager implements \IteratorAggregate
{
    /**
     * Instance
     *
     * @var Manager
     */
    static protected $instance;

    /**
     * Mappers
     *
     * @var MapperInterface[]
     */
    protected $mappers = array();

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
     * Add mapper
     *
     * @param string          $modelClass
     * @param MapperInterface $mapper
     * @return MapperInterface
     * @throws \InvalidArgumentException
     */
    public function add($modelClass, MapperInterface $mapper)
    {
        $modelClass = $this->filterClassName($modelClass);

        if ($this->has($modelClass)) {
            throw new \InvalidArgumentException("Model '{$modelClass}' already defined");
        }

        return $this->mappers[$modelClass] = $mapper;
    }

    /**
     * Get model mapper
     *
     * @param string $modelClass
     * @return MapperInterface
     * @throws \InvalidArgumentException
     */
    public function get($modelClass)
    {
        $modelClass = $this->filterClassName($modelClass);

        if (!$this->has($modelClass)) {
            throw new \InvalidArgumentException("Model '$modelClass' not defined");
        }

        return $this->mappers[$modelClass];
    }

    /**
     * Get all mappers
     *
     * @return MapperInterface[]
     */
    public function getAll()
    {
        return $this->mappers;
    }

    /**
     * Has mapper?
     *
     * @param string $modelClass
     * @return bool
     */
    public function has($modelClass)
    {
        $modelClass = $this->filterClassName($modelClass);

        return isset($this->mappers[$modelClass]);
    }

    /**
     * Iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->getAll();
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