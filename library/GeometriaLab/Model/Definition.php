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
     * @var array
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
    protected function __construct($className)
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
     * @return Definition\Property\PropertyInterface
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
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
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

        if ($this->hasProperty($name)) {
            throw new \InvalidArgumentException("Property with name '$name' already exists");
        }

        if ($tag->getDescription() !== null) {
            throw new \InvalidArgumentException("Not valid JSON params for property '$name'");
        }

        $params = $tag->getParams();
        $params['name'] = $name;

        $this->properties[$name] = static::createProperty($tag->getType(), $params);
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