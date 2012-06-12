<?php

namespace GeometriaLab\Model;

use GeometriaLab\Code\Reflection\DocBlock\Tag\PropertyTag,
    GeometriaLab\Model\Definition\Property\PropertyInterface,
    GeometriaLab\Model\Definition\Property\ModelProperty,
    GeometriaLab\Model\Definition\DefinitionInterface;

use Zend\Code\Reflection\ClassReflection AS ZendClassReflection,
    Zend\Code\Reflection\Exception\InvalidArgumentException as ZendInvalidArgumentException,
    Zend\Code\Reflection\DocBlockReflection AS ZendDocBlockReflection,
    Zend\Code\Reflection\DocBlock\TagManager as ZendTagManager;

class Definition implements DefinitionInterface
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
     * @var ZendTagManager
     */
    static protected $tagManager;

    /**
     * Properties class map
     *
     * @var array
     */
    static protected $propertiesClassMap = array(
        'string'  => 'GeometriaLab\Model\Definition\Property\StringProperty',
        'array'   => 'GeometriaLab\Model\Definition\Property\ArrayProperty',
        'boolean' => 'GeometriaLab\Model\Definition\Property\BooleanProperty',
        'float'   => 'GeometriaLab\Model\Definition\Property\FloatProperty',
        'integer' => 'GeometriaLab\Model\Definition\Property\IntegerProperty',
    );

    /**
     * Protected constructor
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;

        $this->parseDocblock($className);
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
     * @throws \Exception
     */
    public function getProperty($name)
    {
        if (!$this->hasProperty($name)) {
            throw new \Exception("Property '$name' not present in model '$this->className'");
        }

        return $this->properties[$name];
    }

    /**
     * Set property
     *
     * @param string $name
     * @param PropertyInterface $property
     * @return Definition
     */
    public function setProperty($name, PropertyInterface $property)
    {
        $this->properties[$name] = $property;

        return $this;
    }

    /**
     * Create and set property
     *
     * @param string $name
     * @param string $type
     * @param array $params
     * @return PropertyInterface
     */
    public function createAndSetProperty($name, $type, array $params = array())
    {
        $params['name'] = $name;

        $property = static::createProperty($type, $params);

        return $this->setProperty($name, $property);
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
     * Create property by type and params
     *
     * @static
     * @param string $type
     * @param array $params
     * @return PropertyInterface
     * @throws \InvalidArgumentException
     */
    static public function createProperty($type, array $params = array())
    {
        if (isset(static::$propertiesClassMap[$type])) {
            $className = static::$propertiesClassMap[$type];
            $property = new $className($params);
        } else if (class_exists($type)) {
            $params['modelClass'] = $type;
            $property = new ModelProperty($params);
        } else {
            throw new \InvalidArgumentException("Invalid property type '$type'");
        }

        return $property;
    }

    /**
     * Parse class docblock
     *
     * @param string $className
     * @throws \InvalidArgumentException
     */
    protected function parseDocblock($className)
    {
        $classReflection = new ZendClassReflection($className);

        try {
            $docblock = new ZendDocBlockReflection($classReflection, static::getTagManager());
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
     * @param PropertyTag $tag
     * @throws \InvalidArgumentException
     */
    protected function parsePropertyTag(PropertyTag $tag)
    {
        $name = substr($tag->getPropertyName(), 1);

        if ($tag->getDescription() !== null) {
            throw new \InvalidArgumentException("Not valid JSON params for property '$name'");
        }

        if ($this->hasProperty($name)) {
            throw new \InvalidArgumentException("Property with name '$name' already exists");
        }

        $this->createAndSetProperty($name, $tag->getType(), $tag->getParams());
    }

    /**
     * Get tag manager
     *
     * @static
     * @return ZendTagManager
     */
    static protected function getTagManager()
    {
        if (static::$tagManager === null) {
            static::$tagManager = new ZendTagManager(ZendTagManager::USE_DEFAULT_PROTOTYPES);
            static::$tagManager->addTagPrototype(new PropertyTag());
        }

        return static::$tagManager;
    }
}