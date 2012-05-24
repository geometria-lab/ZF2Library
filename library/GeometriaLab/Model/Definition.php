<?php

namespace GeometriaLab\Model;

use GeometriaLab\Code\Reflection\DocBlock\Tag\PropertyTag,
    GeometriaLab\Model\Definition\Property\Factory;

use Zend\Code\Reflection\ClassReflection AS ZendClassReflection,
    Zend\Code\Reflection\Exception\InvalidArgumentException as ZendInvalidArgumentException,
    Zend\Code\Reflection\DocBlockReflection AS ZendDocBlockReflection,
    Zend\Code\Reflection\DocBlock\TagManager as ZendTagManager;

/**
 * @author Ivan Shumkov
 */
class Definition
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
        $property = Factory::factory($tag->getType(), $tag->getParams());
        $property->setName(substr($tag->getVariableName(), 1));

        $this->properties[$property->getName()] = $property;
    }

    static protected function getTagManager()
    {
        if (static::$tagManager === null) {
            static::$tagManager = new ZendTagManager(ZendTagManager::USE_DEFAULT_PROTOTYPES);
            static::$tagManager->addTagPrototype(new PropertyTag());
        }

        return static::$tagManager;
    }
}