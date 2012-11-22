<?php

namespace GeometriaLab\Test\Helper;

use GeometriaLab\Test\TestCaseInterface,
    GeometriaLab\Test\Helper\HelperInterface;

use Zend\Loader\PluginClassLoader as ZendPluginClassLoader,
    Zend\Code\Reflection\DocBlockReflection as ZendDocBlockReflection,
    Zend\Code\Reflection\ClassReflection as ZendClassReflection,
    Zend\Code\Reflection\DocBlock\Tag\PropertyTag as ZendPropertyTag;

// @property \GeometriaLab\Test\Helper\User $user

class HelperBroker
{
    /**
     * @var \PHPUnit_Framework_Test
     */
    protected $test;
    /**
     * @var HelperInterface[]
     */
    protected $initializedHelpers = array();
    /**
     * @var ZendPluginClassLoader
     */
    protected $pluginClassLoader;

    /**
     * @param array $map
     * @throws \InvalidArgumentException
     */
    public function __construct(array $map = array())
    {
        $map = array_merge(
            $this->getMapFromDocBlock(__CLASS__),
            $map
        );

        $this->getPluginClassLoader()->registerPlugins($map);
    }

    /**
     * Get helper by name
     *
     * @param string $name
     * @return HelperInterface
     */
    public function __get($name)
    {
        return $this->getHelper($name);
    }

    /**
     * Get helper by name
     *
     * @param string $name
     * @return HelperInterface
     * @throws \RuntimeException
     */
    public function getHelper($name)
    {
        if (!isset($this->initializedHelpers[$name])) {
            $className = $this->getPluginClassLoader()->load($name);

            if ($className === false) {
                throw new \RuntimeException("Helper with name {$className} doesn't exist");
            }

            if (!class_exists($className, true)) {
                throw new \RuntimeException("{$className} doesn't exist");
            }

            $helper = new $className;

            if (!$helper instanceof HelperInterface) {
                throw new \RuntimeException("{$className} must implement GeometriaLab\\Test\\Helper\\HelperInterface");
            }

            $this->initializedHelpers[$name] = $helper;
            $this->initializedHelpers[$name]->setTest($this->test);
        }

        return $this->initializedHelpers[$name];
    }

    /**
     * Set test case object
     *
     * @param TestCaseInterface $test
     * @return HelperBroker
     */
    public function setTest(TestCaseInterface $test)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Get plugin class loader
     *
     * @return ZendPluginClassLoader
     */
    public function getPluginClassLoader()
    {
        if ($this->pluginClassLoader === null) {
            $this->pluginClassLoader = new ZendPluginClassLoader();
        }

        return $this->pluginClassLoader;
    }

    /**
     * Get all initialized helpers
     *
     * @return HelperInterface[]
     */
    public function getInitializedHelpers()
    {
        return $this->initializedHelpers;
    }

    /**
     * Get helpers class map from DocBlock
     *
     * @param string $className
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getMapFromDocBlock($className)
    {
        $staticMap = array();

        $docComment = new ZendClassReflection($className);
        $comment = $docComment->getDocComment();

        if (!empty($comment)) {
            $docBlock = new ZendDocBlockReflection($comment);
            foreach($docBlock->getTags() as $tag) {
                if ($tag instanceof ZendPropertyTag) {
                    /* @var \Zend\Code\Reflection\DocBlock\Tag\PropertyTag $tag */
                    $name = substr($tag->getPropertyName(), 1);
                    $staticMap[$name] = $tag->getType();
                }
            }
        }

        return $staticMap;
    }
}
