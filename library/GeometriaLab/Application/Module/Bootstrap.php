<?php

class GeometriaLab_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap
{
    /**
     * Force frontcontroller to be registered
     * to prevent include_once warning
     *
     * @var $_pluginResources array
     */
    //protected $_pluginResources = array('frontcontroller' => null);

    public function getResourceLoader()
    {
        if ((null === $this->_resourceLoader)
            && (false !== ($namespace = $this->getAppNamespace()))
        ) {
            $r    = new ReflectionClass($this);
            $path = $r->getFileName();
            $this->setResourceLoader(new GeometriaLab_Application_Module_Autoloader(array(
                'namespace' => $namespace,
                'basePath'  => dirname($path),
            )));
        }

        return $this->_resourceLoader;
    }

    /**
     * @see Zend_Application_Module_Bootstrap::registerPluginResource()
     *
    public function registerPluginResource($resource, $options = null)
    {
        // force resource names to be in lower case
        if (is_string($resource)) {
            $resource = strtolower($resource);
        }
        return parent::registerPluginResource($resource, $options);
    }
     */
    /*
	protected function _initModuleEnabled()
	{
		$enabledKey = $this->getModuleName() . 'Enabled';

		try {
			$layout = $this->getApplication()->bootstrap('layout')->getResource('layout');
            if (null !== $layout) {
                $layout->$enabledKey = true;
            }
		} catch (Zend_Application_Bootstrap_Exception $e) {
			// catch exception if could not bootstrap layout
			// and do none because it is not a big problem
		}
		Zend_Registry::set($enabledKey, true);
	}
    */
}