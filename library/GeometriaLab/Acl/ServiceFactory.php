<?php

namespace GeometriaLab\Acl;

use Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface,
    Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\GenericRole as ZendGenericRole,
    Zend\Permissions\Acl\Resource\ResourceInterface as ZendResource,
    Zend\Permissions\Acl\Resource\GenericResource as ZendGenericResource,
    Zend\Permissions\Acl\Exception\InvalidArgumentException as ZendAclInvalidArgumentException,
    Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\Stdlib\Glob as ZendGlob,
    GeometriaLab\Acl\Acl,
    GeometriaLab\Acl\Exception\AccessDenied;

class ServiceFactory implements ZendFactoryInterface
{
    const ACL_DIR = 'Acl';
    const CONTROLLER_DIR = 'Controller';

    /**
     * @var Acl
     */
    private $acl;

    /**
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return Acl
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        //$config = $serviceLocator->get('Configuration');

        $this->acl = new Acl();

        $this->addRoles();

        foreach ($serviceLocator->get('ModuleManager')->getLoadedModules() as $moduleName => $value) {
            $this->addAcl($moduleName);
        }

        $routeMatch = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch();

        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');

        if ($controller !== null && $action !== null) {
            $this->acl->setCurrentResource($controller);
            $this->acl->setCurrentPrivilege($action);
        }

        return $this->acl;
    }
    /**
     * @return ServiceFactory
     */
    private function addRoles()
    {
        $this->acl->addRole(new ZendGenericRole(Acl::ROLE_GUEST))
                  ->addRole(new ZendGenericRole(Acl::ROLE_USER), Acl::ROLE_GUEST)
                  ->addRole(new ZendGenericRole(Acl::ROLE_MANAGER), Acl::ROLE_USER)
                  ->addRole(new ZendGenericRole(Acl::ROLE_ADMIN), Acl::ROLE_MANAGER)
                  ->addRole(new ZendGenericRole(Acl::ROLE_SUPER_ADMIN), Acl::ROLE_ADMIN);

        return $this;
    }
    /**
     * @param $moduleName
     * @return ServiceFactory
     */
    private function addAcl($moduleName)
    {
        $pathPattern = $this->getAclPath($moduleName) . '*';
        foreach (ZendGlob::glob($pathPattern, ZendGlob::GLOB_BRACE) as $file) {
            /* @var \GeometriaLab\Acl\Resource $resource */
            $resourceName = '\\' . $moduleName . '\\' . self::ACL_DIR . '\\' . ucfirst(pathinfo($file, PATHINFO_FILENAME));
            $resourceId = $moduleName . '\\' . self::CONTROLLER_DIR . '\\' . ucfirst(pathinfo($file, PATHINFO_FILENAME));
            $resource = new $resourceName($resourceId);

            $this->acl->addResource($resource);

            $resource->createRoles($this->acl);
            $resource->createRules($this->acl);
            $resource->setCurrentRole($this->acl->getCurrentRole());
        }
        return $this;
    }
    /**
     * @param $moduleName
     * @return string
     */
    private function getAclPath($moduleName)
    {
        return 'module' . DIRECTORY_SEPARATOR
            . $moduleName . DIRECTORY_SEPARATOR
            . 'src' . DIRECTORY_SEPARATOR
            . $moduleName . DIRECTORY_SEPARATOR
            . self::ACL_DIR . DIRECTORY_SEPARATOR;
    }
}
