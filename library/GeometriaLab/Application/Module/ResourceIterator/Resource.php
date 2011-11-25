<?php

/**
 * @author Ivan Shumkov
 */
class GeometriaLab_Application_Module_ResourceIterator_Resource extends SplFileInfo
{
    protected $_className;

    protected $_namespace;

    /**
     * @var ReflectionClass
     */
    protected $_reflection;

    public function __construct(SplFileInfo $file, $namespace)
    {
        parent::__construct($file);

        $this->_namespace = $namespace;
    }

    public function getClassName()
    {
        if (!$this->_className) {
            $this->_className = $this->_namespace . '_' . substr($this->getBasename(), 0, -4);
        }

        return $this->_className;
    }

    public function getNamespace()
    {
        return $this->_namespace;
    }

    public function isAbstract()
    {
        return $this->_getReflection()->isAbstract();
    }

    public function isSubclassOf($className)
    {
        return $this->_getReflection()->isSubclassOf($className);
    }

    protected function _getReflection()
    {
        if (!$this->_reflection) {
            $this->_reflection = new ReflectionClass($this->getClassName());
        }

        return $this->_reflection;
    }
}