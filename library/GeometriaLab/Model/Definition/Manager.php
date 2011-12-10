<?php

/**
 * @author Ivan Shumkov
 */
class GeometriaLab_Model_Definition_Manager
{
    /**
     * Instance
     *
     * @var GeometriaLab_Model_Definition_Manager
     */
    static protected $_instance;

    /**
     * Pro
     *
     * @var array
     */
    protected $_definitions = array();

    /**
     * Get instance
     *
     * @static
     * @return GeometriaLab_Model_Definition_Manager
     */
    static public function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Add model
     *
     * @param string $modelClass
     * @return GeometriaLab_Model_Definition
     * @throws GeometriaLab_Model_Exception
     */
    public function add($modelClass)
    {
        if ($this->has($modelClass)) {
            throw new GeometriaLab_Model_Exception("Model '$modelClass' already defined");
        }

        return $this->_definitions[$modelClass] = new GeometriaLab_Model_Definition($modelClass);
    }

    /**
     * Get model definition
     *
     * @param string $modelClass
     * @return GeometriaLab_Model_Definition
     * @throws GeometriaLab_Model_Exception
     */
    public function get($modelClass)
    {
        if (!$this->has($modelClass)) {
            throw new GeometriaLab_Model_Exception("Model '$modelClass' not defined");
        }

        return $this->_definitions[$modelClass];
    }

    public function has($modelClass)
    {
        return isset($this->_definitions[$modelClass]);
    }
}