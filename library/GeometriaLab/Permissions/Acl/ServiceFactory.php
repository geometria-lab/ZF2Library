<?php

namespace GeometriaLab\Permissions\Acl;

use GeometriaLab\Api\Mvc\Router\Http\Api;

use Zend\Stdlib\Glob as ZendGlob,

    Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface,

    Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\GenericRole as ZendGenericRole;

class ServiceFactory implements ZendFactoryInterface
{
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

        $controllerNameSpace = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch()->getParam('__NAMESPACE__');

        $this->addRoles();
        $this->addResources($controllerNameSpace);

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
     * @throws \InvalidArgumentException
     */
    private function addRoles()
    {
        if (isset($this->config['roles']) && is_array($this->config['roles'])) {
            foreach ($this->config['roles'] as $role) {
                if (is_array($role)) {
                    if (!isset($role['name'])) {
                        throw new \InvalidArgumentException('Need name');
                    }

                    $roleId = $role['name'];
                    $parent = isset($role['parent']) ? $role['parent'] : null;
                } else {
                    $roleId = $role;
                    $parent = null;
                }

                $this->getAcl()->addRole(new ZendGenericRole($roleId), $parent);
            }
        }
        return $this;
    }

    /**
     * @param string $controllerNamespace
     * @return ServiceFactory
     */
    private function addResources($controllerNamespace)
    {
        $namespace = $this->getNamespace();
        $pathPattern = $this->getResourcesPath() . '*';

        foreach (ZendGlob::glob($pathPattern, ZendGlob::GLOB_BRACE) as $file) {
            /* @var \GeometriaLab\Permissions\Acl\Resource $resource */
            $resourceName = $namespace . '\\' . ucfirst(pathinfo($file, PATHINFO_FILENAME));
            $resourceId = $controllerNamespace . '\\' . ucfirst(pathinfo($file, PATHINFO_FILENAME));
            $resource = new $resourceName($resourceId);

            $this->getAcl()->addResource($resource);

            $resource->createRoles($this->getAcl());
            $resource->createRules($this->getAcl());
        }
        return $this;
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getNamespace()
    {
        if (empty($this->config['__NAMESPACE__'])) {
            throw new \InvalidArgumentException('Need not empty "acl.__NAMESPACE__" param in config');
        }
        return $this->config['__NAMESPACE__'];
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getResourcesPath()
    {
        if (empty($this->config['base_dir'])) {
            throw new \InvalidArgumentException('Need not empty "acl.base_dir" param in config');
        }
        return rtrim($this->config['base_dir'], '/') . '/';
    }
}
