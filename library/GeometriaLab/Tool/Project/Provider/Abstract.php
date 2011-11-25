<?php

class GeometriaLab_Tool_Project_Provider_Abstract extends Zend_Tool_Project_Provider_Abstract
{
    public function getName()
    {
        $className = get_class($this);
        $providerName = $className;

        $providerName = str_replace('Tool_', '', $providerName);
        $providerName = str_replace('_', '', $providerName);

        if (substr($providerName, -8) == 'Provider') {
            $providerName = substr($providerName, 0, strlen($providerName)-8);
        }
        return $providerName;
    }
}