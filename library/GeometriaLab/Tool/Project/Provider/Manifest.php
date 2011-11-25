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
            || define('APPLICATION_PATH', (getenv('APPLICATION_PATH') ? getenv('APPLICATION_PATH') : getcwd() . '/application'));

        // Define application environment
        defined('APPLICATION_ENV')
            || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

        $application = new Zend_Application(APPLICATION_ENV, $this->_getApplicationConfig());
        $application->bootstrap();

        $frontController = Zend_Controller_Front::getInstance();
        $frontController->setParam('bootstrap', $application->getBootstrap());
    }

    protected function _getApplicationConfig()
    {
        $configPath = getenv('APPLICATION_CONFIG_PATH') ? getenv('APPLICATION_CONFIG_PATH')
                                                        : APPLICATION_PATH . '/configs/application.ini';

        $bootstrapPath = getenv('APPLICATION_BOOTSTRAP_PATH') ? getenv('APPLICATION_BOOTSTRAP_PATH')
                                                              : APPLICATION_PATH . '/bootstraps/Cli.php';

        $bootstrapClass = getenv('APPLICATION_BOOTSTRAP_CLASS') ? getenv('APPLICATION_BOOTSTRAP_CLASS')
                                                                : 'Bootstrap_Cli';

        // todo: Move to YAML
        $config = new Zend_Config_Ini($configPath, APPLICATION_ENV, true);
        $config->merge(new Zend_Config(array(
            'phpSettings' => array(
                'display_errors' => 1
            ),
            'bootstrap' => array(
                'path'  => $bootstrapPath,
                'class' => $bootstrapClass
            )
        )));

        if (isset ($config->resources->frontcontroller->plugins)) {
            unset($config->resources->frontcontroller->plugins);
        }
        if (isset ($config->resources->layout)) {
            unset($config->resources->layout);
        }

        return $config;
    }
}
