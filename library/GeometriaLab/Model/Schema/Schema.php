<?php

namespace GeometriaLab\Model\Schema;

use GeometriaLab\Model\Schema\Property\PropertyInterface;

use Zend\Code\Reflection\DocBlock\Tag\TagInterface as ZendTagInterface,
    Zend\Code\Reflection\DocBlock\Tag\PropertyTag as ZendPropertyTag,
    Zend\Code\Reflection\ClassReflection AS ZendClassReflection,
    Zend\Code\Reflection\Exception\InvalidArgumentException as ZendInvalidArgumentException,
    Zend\Code\Reflection\DocBlockReflection AS ZendDocBlockReflection,
    Zend\Serializer\Serializer as ZendSerializer;

class Schema
{
    /**
     * Class name
     *
     * @var string
     */
    protected $className;

    /**
     * Properties
     *
     * @var PropertyInterface[]
     */
    protected $properties = array();

    /**
     * Regular properties class map
     *
     * @var array
     */
    static protected $regularPropertiesClassMap = array(
        'string'  => 'GeometriaLab\Model\Schema\Property\StringProperty',
        'array'   => 'GeometriaLab\Model\Schema\Property\ArrayProperty',
        'boolean' => 'GeometriaLab\Model\Schema\Property\BooleanProperty',
        'float'   => 'GeometriaLab\Model\Schema\Property\FloatProperty',
        'integer' => 'GeometriaLab\Model\Schema\Property\IntegerProperty',
    );

    /**
     * Model property class name
     *
     * @var string
     */
    static protected $modelPropertyClass = '\GeometriaLab\Model\Schema\Property\ModelProperty';

    /**
     * Params serializer adapter
     *
     * @var string
     */
    static protected $paramsSerializerAdapter = 'json';

    /**
     * Protected constructor
     *
     * @param string $className
     */
    public function __construct($className = null)
    {
        if ($className !== null) {
            $this->setClassName($className);
            $this->parseDocblock($className);
        }
    }

    /**
     * Set class name
     *
     * @param $className
     * @return Schema
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get class name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Get property
     *
     * @param string $name
     * @return PropertyInterface
     * @throws \InvalidArgumentException
     */
    public function getProperty($name)
    {
        if (!$this->hasProperty($name)) {
            throw new \InvalidArgumentException("Property '$name' not present in model '$this->className'");
        }

        return $this->properties[$name];
    }

    /**
     * Set property
     *
     * @param PropertyInterface $property
     * @return Schema
     */
    public function setProperty(PropertyInterface $property)
    {
        $this->properties[$property->getName()] = $property;

        return $this;
    }

    /**
     * Has property?
     *
     * @param string $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * Get all properties
     *
     * @return PropertyInterface[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Parse class docblock
     *
     * @todo Move from schema to standalone class (Zend/Code/.../Annotations?)
     * @param string $className
     * @throws \InvalidArgumentException
     */
    protected function parseDocblock($className)
    {
        $classReflection = new ZendClassReflection($className);

        try {
            $docblock = new ZendDocBlockReflection($classReflection);
        } catch (ZendInvalidArgumentException $e) {
            throw new \InvalidArgumentException('Docblock not present');
        }

        /**
         * @var \Zend\Code\Reflection\DocBlock\Tag\TagInterface $tag
         */
        foreach($docblock->getTags() as $tag) {
            $methodName = "parse{$tag->getName()}Tag";
            if (method_exists($this, $methodName)) {
                call_user_func(array($this, $methodName), $tag);
            }
        }
    }

    /**
     * Parse property tag
     *
     * @param ZendPropertyTag $tag
     * @throws \InvalidArgumentException
     */
    protected function parsePropertyTag(ZendPropertyTag $tag)
    {
        $name = substr($tag->getPropertyName(), 1);

        if ($this->hasProperty($name)) {
            throw new \InvalidArgumentException("Property with name '$name' already exists");
        }

        $params = $this->getParamsFromTag($tag);
        $params['name'] = $name;

        $type = $tag->getType();

        if (strpos($type, '[]') === strlen($type) - 2) {
            $itemPropertyType = substr($type, 0, strlen($type) - 2);
            $params['itemProperty'] = static::createProperty($itemPropertyType);
            $type = 'array';
        }

        $property = static::createProperty($type, $params);

        $this->setProperty($property);
    }

    /**
     * Get params from tag
     *
     * @param \Zend\Code\Reflection\DocBlock\Tag\TagInterface $tag
     * @return array|mixed
     * @throws \InvalidArgumentException
     */
    protected function getParamsFromTag(ZendTagInterface $tag)
    {
        $description = $tag->getDescription();

        if ($description == '') {
            return array();
        }

        $description = preg_replace('#\s+#m', ' ', $description);

        $params = ZendSerializer::unserialize($description, static::$paramsSerializerAdapter);

        if (!is_array($params)) {
            $name = substr($tag->getPropertyName(), 1);
            throw new \InvalidArgumentException("Not valid params for property '$name'");
        }

        return $params;
    }

    /**
     * Create property by type and params
     *
     * @static
     * @param string $type
     * @param array $params
     * @return PropertyInterface
     * @throws \InvalidArgumentException
     */
    static protected function createProperty($type, array $params = array())
    {
        if (isset(static::$regularPropertiesClassMap[$type])) {
            $property = new static::$regularPropertiesClassMap[$type]($params);
        } else if (class_exists($type)) {
            $params['modelClass'] = $type;

            $property = new static::$modelPropertyClass($params);
        } else {
            throw new \InvalidArgumentException("Invalid property type '$type'");
        }

        return $property;
    }
}