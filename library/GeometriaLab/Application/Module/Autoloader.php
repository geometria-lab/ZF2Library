<?php

class GeometriaLab_Application_Module_Autoloader extends Zend_Application_Module_Autoloader
{
    public function initDefaultResourceTypes()
    {
        parent::initDefaultResourceTypes();

        $this->addResourceTypes(array(
            'tool' => array(
                'path'      => 'tools',
                'namespace' => 'Tool',
            )
        ));
    }
}