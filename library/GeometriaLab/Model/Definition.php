<?php

namespace GeometriaLab\Model;

use GeometriaLab\Code\Reflection\DocBlock\Tag\PropertyTag,
    GeometriaLab\Model\Definition\Property\Factory,
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
     * Constructor
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
     * @throws \Exception
     */
    protected function parseDocblock($className)
    {
        $classReflection = new ZendClassReflection($className);

        try {
            $docblock = new ZendDocBlockReflection($classReflection, static::getTagManager());
        } catch (ZendInvalidArgumentException $e) {
            throw new \Exception('Docblock not present');
        }

        /**
         * @var \Zend\Code\Reflection\DocBlock\TagInterface $tag
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
     * @throws \Exception
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

        $this->properties[$name] = Factory::factory($tag->getType(), $params);
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