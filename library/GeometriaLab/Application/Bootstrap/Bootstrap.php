<?php

class GeometriaLab_Application_Bootstrap_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function getResourceLoader()
    {
        if (null === $this->_resourceLoader) {
            $this->setResourceLoader(new GeometriaLab_Application_Module_Autoloader(array(
                'namespace' => '',
                'basePath'  => APPLICATION_PATH,
            )));
        }

        return $this->_resourceLoader;
    }
}