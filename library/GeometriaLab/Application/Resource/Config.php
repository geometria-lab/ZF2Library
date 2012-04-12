<?php

class GeometriaLab_Application_Resource_Config extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $config = $this->getBootstrap()->getOptions();

        if (isset($config['resources'])) {
            $config = array_merge($config, $config['resources']);

            unset($config['resources']);
        }

        $config = new Zend_Config($config);

        Zend_Registry::set('config', $config);

        return $config;
    }
}