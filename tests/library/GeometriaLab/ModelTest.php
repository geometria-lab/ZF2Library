<?php


class GeometriaLab_ModelTest extends GeometriaLab_Model_Schemaless
{
    /**
     * Class name
     *
     * @var string
     */
    protected $_className;

    /**
     * Constructor
     *
     * @param mixed $data Model data (must be array or iterable object)
     */
    public function __construct($data = null)
    {
        $this->_setup();

        parent::__construct($data);
    }

    /**
     * Populate model from array or iterable object
     *
     * @param array|Traversable $data                      Model data (must be array or iterable object)
     * @param bool              $ignoreUndefinedProperties
     * @return GeometriaLab_Model
     * @throws GeometriaLab_Model_Exception
     */
    public function populate($data, $ignoreUndefinedProperties = false)
    {
        if (!GeometriaLab_Validate_IsIterable::staticIsValid($data)) {
            throw new GeometriaLab_Model_Exception("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            if ($this->_getPropertyDefinition($key) !== null) {
                $this->set($key, $value);
            } else if (!$ignoreUndefinedProperties) {
                throw new GeometriaLab_Model_Exception("Trying to set property $key, that not exists in object $this->_className");
            }
        }

        return $this;
    }

    /**
     * Get property
     *
     * @param $name
     * @return mixed
     * @throws GeometriaLab_Model_Exception
     */
    public function get($name)
    {
        $property = $this->_getPropertyDefinition($name);
        if ($property === null) {
            throw new GeometriaLab_Model_Exception("Property '$name' does not exists");
        }

        $method = "get{$name}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        }

        return $this->_propertyValues[$name];
    }

    /**
     * Set property
     *
     * @param $name
     * @param $value
     * @return GeometriaLab_Model
     * @throws GeometriaLab_Model_Exception
     */
    public function set($name, $value)
    {
        $property = $this->_getPropertyDefinition($name);
        if ($property === null) {
            throw new GeometriaLab_Model_Exception("Property '$name' does not exists");
        }
        if (!$property->isValid($value)) {
            throw new GeometriaLab_Model_Exception("Invalid value for property '$name'");
        }

        $method = "set{$name}";
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method), $value);
        }

        $this->_propertyValues[$name] = $value;

        return $this;
    }

    /**
     * Setup model
     */
    protected function _setup()
    {
        $className = $this->_getClassName();

        $definitions = GeometriaLab_Model_Definition_Manager::getInstance();

        if (!$definitions->has($className)) {
            $definitions->define($className);
        }

        foreach($this->_getProperties() as $name => $property) {
            $this->setProperty($name, $property->getDefaultValue());
        }
    }

    /**
     * Get class name
     *
     * @return string
     */
    protected function _getClassName()
    {
        if ($this->_className === null) {
            $this->_className = get_class($this);
        }

        return $this->_className;
    }

    /**
     * Get properties
     *
     * @return array
     */
    protected function _getProperties()
    {
        $className = $this->_getClassName();

        $definitions = GeometriaLab_Model_Definition_Manager::getInstance();

        return $definitions->get($className)->getProperties();
    }

    /**
     * Get property definition
     *
     * @param string $name
     * @return GeometriaLab_Model_Definition_Property|null
     */
    protected function _getPropertyDefinition($name)
    {
        $className = $this->_getClassName();

        $definitions = GeometriaLab_Model_Definition_Manager::getInstance();

        if ($definitions->get($className)->hasProperty($name)) {
            return $definitions->get($className)->getProperty($name);
        } else {
            return null;
        }
    }
}