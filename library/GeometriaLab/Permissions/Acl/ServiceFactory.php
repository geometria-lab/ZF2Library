<?php

namespace GeometriaLab\Permissions\Acl;

use GeometriaLab\Api\Mvc\Router\Http\Api;

use Zend\Stdlib\Glob as ZendGlob,
    Zend\Mvc\MvcEvent as ZendMvcEvent,

    Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface,

    Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\GenericRole as ZendGenericRole,
    Zend\Permissions\Acl\Resource\GenericResource as ZendGenericResource,
    Zend\Permissions\Acl\Resource\ResourceInterface as ZendResource,
    Zend\Permissions\Acl\Exception\InvalidArgumentException as ZendAclInvalidArgumentException;

class ServiceFactory implements ZendFactoryInterface
{
    const ACL_DIR = 'Acl';
    const CONTROLLER_DIR = 'Controller';

    /**
     * @var ZendAcl
     */
    private $acl;
    /**
     * @var array
     */
    private $config = array();

    /**
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return ZendAcl
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');
        if (isset($config['acl'])) {
            $this->setConfig($config['acl']);
        }

        $this->addRoles();

        $controllerNameSpace = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch()->getParam('__NAMESPACE__');
        $moduleName = explode('\\', $controllerNameSpace);
        $this->addResources($moduleName[1]);

        return $this->getAcl();
    }
    /**
     * @param array $config
     * @return ServiceFactory
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }
    /**
     * @return ZendAcl
     */
    public function getAcl()
    {
        if ($this->acl === null) {
            $this->acl = new ZendAcl();
        }
        return $this->acl;
    }
    /**
     * @return ServiceFactory
     */
    private function addRoles()
    {
        if (isset($this->config['roles']) && is_array($this->config['roles'])) {
            foreach ($this->config['roles'] as $role) {
                $this->getAcl()->addRole(new ZendGenericRole($role));
            }
        }
        return $this;
    }
    /**
     * @param $moduleName
     * @return ServiceFactory
     */
    private function addResources($moduleName)
    {
        $pathPattern = $this->getResourcesPath($moduleName) . '*';
        foreach (ZendGlob::glob($pathPattern, ZendGlob::GLOB_BRACE) as $file) {
            /* @var \GeometriaLab\Permissions\Acl\Resource $resource */
            $resourceName = '\\' . Api::API_MODULE_DIR . '\\' . $moduleName . '\\' . self::ACL_DIR . '\\' . ucfirst(pathinfo($file, PATHINFO_FILENAME));
            $resourceId = Api::API_MODULE_DIR . '\\' . $moduleName . '\\' . self::CONTROLLER_DIR . '\\' . ucfirst(pathinfo($file, PATHINFO_FILENAME));
            $resource = new $resourceName($resourceId);

            $this->getAcl()->addResource($resource);

            $resource->createRoles($this->getAcl());
            $resource->createRules($this->getAcl());
        }
        return $this;
    }
    /**
     * @param $moduleName
     * @return string
     */
    private function getResourcesPath($moduleName)
    {
        return 'module' . DIRECTORY_SEPARATOR
            . Api::API_MODULE_DIR . DIRECTORY_SEPARATOR
            . $moduleName . DIRECTORY_SEPARATOR
            . 'src' . DIRECTORY_SEPARATOR
            . $moduleName . DIRECTORY_SEPARATOR
            . self::ACL_DIR . DIRECTORY_SEPARATOR;
    }
}
