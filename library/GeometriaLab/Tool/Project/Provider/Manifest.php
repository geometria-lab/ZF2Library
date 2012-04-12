<?php

class GeometriaLab_Tool_Project_Provider_Manifest implements Zend_Tool_Framework_Manifest_ProviderManifestable
{
    public function getProviders()
    {
        $this->_bootstrapApplication();

        $providers = array();

        $tools = new GeometriaLab_Application_Module_ResourceIterator('tool');
        foreach($tools as $tool) {
            if ($tool->isSubClassOf('Zend_Tool_Project_Provider_Abstract')) {
                $className = $tool->getClassname();
                $providers[] = new $className();
            }
        }

        return $providers;
    }

    protected function _bootstrapApplication()
    {
        // Define path to application directory
        defined('APPLICATION_PATH')
            || define('APPLICATION_PATH', (getenv('APPLICATION_PATH') !== null ? getenv('APPLICATION_PATH') : getcwd() . '/application'));

        // Define application environment
        defined('APPLICATION_ENV')
            || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') !== null ? getenv('APPLICATION_ENV') : 'production'));

        $application = new Zend_Application(APPLICATION_ENV, $this->_getApplicationConfig());
        $application->bootstrap();

        $frontController = Zend_Controller_Front::getInstance();
        $frontController->setParam('bootstrap', $application->getBootstrap());
    }

    protected function _getApplicationConfig()
    {
        $vars = array('APPLICATION_CONFIG_PATH',
                      'APPLICATION_CONFIG_CLASS',
                      'APPLICATION_BOOTSTRAP_PATH',
                      'APPLICATION_BOOTSTRAP_CLASS');

        foreach ($vars as $var) {
            if (getenv($var) === null) {
                throw new Zend_Tool_Project_Provider_Exception("$var not present!");
            }
        }

        $configClass = getenv('APPLICATION_CONFIG_CLASS');

        $config = new $configClass(getenv('APPLICATION_CONFIG_PATH'), APPLICATION_ENV, true);

        $config->merge(new Zend_Config(array(
            'bootstrap' => array(
                'path'  => getenv('APPLICATION_BOOTSTRAP_PATH'),
                'class' => getenv('APPLICATION_BOOTSTRAP_CLASS')
            )
        )));

        return $config;
    }
}
