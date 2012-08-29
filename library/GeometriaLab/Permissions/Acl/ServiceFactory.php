<?php

namespace GeometriaLab\Permissions\Acl;

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
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return ZendAcl
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');

        $this->addRoles();

        $controllerName = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch()->getParam('__NAMESPACE__');
        $moduleName = explode('\\', $controllerName);
        $this->addAcl(array_shift($moduleName));

        return $this->getAcl();
    }
    /**
     * @return ServiceFactory
     */
    private function addRoles()
    {
        $this->getAcl()->addRole(new ZendGenericRole('guest'));

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
            /* @var \GeometriaLab\Permissions\Acl\Resource $resource */
            $resourceName = '\\' . $moduleName . '\\' . self::ACL_DIR . '\\' . ucfirst(pathinfo($file, PATHINFO_FILENAME));
            $resourceId = $moduleName . '\\' . self::CONTROLLER_DIR . '\\' . ucfirst(pathinfo($file, PATHINFO_FILENAME));
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
    private function getAclPath($moduleName)
    {
        return 'module' . DIRECTORY_SEPARATOR
            . $moduleName . DIRECTORY_SEPARATOR
            . 'src' . DIRECTORY_SEPARATOR
            . $moduleName . DIRECTORY_SEPARATOR
            . self::ACL_DIR . DIRECTORY_SEPARATOR;
    }
}
