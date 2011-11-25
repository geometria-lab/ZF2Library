<?php

/**
 * @author Ivan Shumkov
 */
class GeometriaLab_Application_Module_ResourceIterator implements IteratorAggregate
{
    protected $_resourceType;

    public function __construct($resourceType)
    {
        $this->setResourceType($resourceType);
    }

    public function setResourceType($type)
    {
        $this->_resourceType = $type;

        return $this;
    }

    public function getResourceType()
    {
        return $this->_resourceType;
    }

    public function getIterator()
    {
        return new ArrayObject($this->_getResources());
    }

    protected function _getResources()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $bootstrap = $frontController->getParam('bootstrap');
        $resourceLoaders = $bootstrap->getApplication()
                                     ->getAutoloader()
                                     ->getAutoloaders();

        $resources = array();

        foreach($resourceLoaders as $resourceLoader) {
            $moduleResources = $this->_getResourcesByLoader($resourceLoader);
            $resources = array_merge($resources, $moduleResources);
        }

        return $resources;
    }

    protected function _getResourcesByLoader(Zend_Loader_Autoloader_Resource $resourceLoader)
    {
        $type = $this->getResourceType();
        $resourceTypes = $resourceLoader->getResourceTypes();

        if (!$resourceLoader->hasResourceType($type)) {
            throw new GeometriaLab_Application_Module_ResourceIterator_Exception("Unknown resource type $type");
        }

        $path = $resourceTypes[$type]['path'];
        $namespace = $resourceTypes[$type]['namespace'];

        $resources = array();

        if (!is_dir($path)) {
            return $resources;
        }

        foreach (new RecursiveDirectoryIterator($path) as $file) {
            if (!$file->isFile() || substr($file->getBasename(), -4) != '.php') {
                continue;
            }

            $resources[] = new GeometriaLab_Application_Module_ResourceIterator_Resource($file, $namespace);
        }

        return $resources;
    }
}
