<?php

/**
 * @author Ivan Shumkov
 */
class GeometriaLab_Model_Definition
{
    /**
     * Class name
     *
     * @var string
     */
    protected $_className;

    /**
     * Properties
     *
     * @var array
     */
    protected $_properties = array();

    /**
     * Constructor
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->_className = $className;

        $this->_parseDocblock($className);
    }

    /**
     * Get class name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->_className;
    }

    /**
     * Get property
     *
     * @param string $name
     * @return GeometriaLab_Model_Definition_Property
     * @throws GeometriaLab_Model_Exception
     */
    public function getProperty($name)
    {
        if (!$this->hasProperty($name)) {
            throw new GeometriaLab_Model_Exception("Property '$name' not present in model '$this->_className'");
        }

        return $this->_properties[$name];
    }

    /**
     * Has property?
     *
     * @param string $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return isset($this->_properties[$name]);
    }

    /**
     * Get all properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Parse class docblock
     *
     * @param string $className
     */
    protected function _parseDocblock($className)
    {
        $reflection = new Zend_Reflection_Class($className);

        try {
            $docblock = $reflection->getDocblock();
        } catch (Zend_Reflection_Exception $e) {

        }

        if (isset($docblock)) {
            foreach($docblock->getTags() as $tag) {
                switch ($tag->getName()) {
                    case 'property':
                        $this->_parsePropertyTag($tag);

                        break;
                }
            }
        }
    }

    /**
     * Parse property tag
     *
     * @param Zend_Reflection_Docblock_Tag $tag
     * @return GeometriaLab_Model_Definition_Property_Abstract
     * @throws GeometriaLab_Model_Definition_Exception
     */
    protected function _parsePropertyTag(Zend_Reflection_Docblock_Tag $tag)
    {
        $parts = preg_split('/(string|integer|float|boolean)?(\[\]) +/', trim($tag->getDescription()), 3);

        // Validate type
        if (!isset($parts[0])) {
            throw new GeometriaLab_Model_Definition_Exception('Property type not defined');
        }

        if (!preg_match('//', $parts[0], $match)) {
            throw new GeometriaLab_Model_Definition_Exception('Invalid property type. Allowed: ' . json_encode($allowedProperties));
        }

        $type = $match[1];


        // Validate name
        if (!isset($parts[1])) {
            throw new GeometriaLab_Model_Definition_Exception('Property name not defined');
        }
        $name = $parts[1];
        if (0 !== strpos($name, '$')) {
            throw new GeometriaLab_Model_Definition_Exception('Property name must be start with $');
        }
        $name = substr($name, 1);

        // Parse params
        if (isset($parts[2])) {
            $params = json_decode($parts[2]);

            if ($params === false || !is_object($params)) {
                throw new GeometriaLab_Model_Definition_Exception('Invalid params format, must be JSON');
            }
        } else {
            $params = new stdClass();
        }

        $className = "GeometriaLab_Model_Definition_Property_" . ucfirst($type);

        $property = new $className;
        $property->setName($name);

        if (isset($params->defaultValue)) {
            $property->setDefaultValue($params->defaultValue);
        }

        $this->_properties[$property->getName()] = $property;

        return $property;
    }
}