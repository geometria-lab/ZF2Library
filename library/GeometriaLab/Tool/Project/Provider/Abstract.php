<?php

class GeometriaLab_Tool_Project_Provider_Abstract extends Zend_Tool_Project_Provider_Abstract
{
    /**
     * @var Zend_Tool_Framework_Client_Storage
     */
    protected $_storage;

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

    protected function _getStorage()
    {
        if (null == $this->_storage) {
            $path = APPLICATION_PATH . '/../.zf/storage';
            if (!is_dir($path)) {
                mkdir($path);
            }
            $adapter = new Zend_Tool_Framework_Client_Storage_Directory($path);
            $this->_registry->getStorage()->setAdapter($adapter);

            $this->_storage = $this->_registry->getStorage();
        }

        return $this->_storage;
    }
}