<?php

namespace GeometriaLab\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Resource\ResourceInterface as ZendResource,
    Zend\Permissions\Acl\Resource\GenericResource as ZendGenericResource,
    Zend\Permissions\Acl\Role\GenericRole as ZendGenericRole,
    Zend\Permissions\Acl\Exception\InvalidArgumentException as ZendAclInvalidArgumentException,
    Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\Stdlib\Glob as ZendGlob,
    GeometriaLab\Acl\Acl,
    GeometriaLab\Acl\Exception\AccessDenied;

class Service
{
    const ACL_DIR = 'Acl';
    const SERVICE_DIR = 'Controller';

    private $moduleName;
    /**
     * @var ZendMvcEvent
     */
    private $e;
    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @return Acl
     */
    public function getAcl()
    {
        if ($this->acl === null) {
            $this->acl = new Acl();
        }
        return $this->acl;
    }
    /**
     * @return string
     */
    private function getAclPath()
    {
        return 'module' . DIRECTORY_SEPARATOR
            . $this->moduleName . DIRECTORY_SEPARATOR
            . 'src' . DIRECTORY_SEPARATOR
            . $this->moduleName . DIRECTORY_SEPARATOR
            . self::ACL_DIR . DIRECTORY_SEPARATOR;
    }
    /**
     * @param string $moduleName
     * @return Service
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }
    /**
     * @param ZendMvcEvent $e
     * @return Service
     */
    public function setEvent(ZendMvcEvent $e)
    {
        $this->e = $e;
        return $this;
    }
    /**
     * @return bool
     */
    public function dispatch()
    {
        $this->addRoles();
        $this->addAcl();
        $controller = $this->e->getRouteMatch()->getParam('controller');
        $action = $this->e->getRouteMatch()->getParam('action');
        if ($controller === null || $action === null) {
            return false;
        }
        //$currentResource = $this->getAcl()->getResource($controller);
        if (!$this->getAcl()->isAllowed(null, $controller, $action)) {
            throw new AccessDenied('Access denied');
        }
        return true;
    }
    /**
     * @return Service
     */
    private function addRoles()
    {
        $acl = $this->getAcl();
        $acl->addRole(new ZendGenericRole(Acl::ROLE_GUEST))
            ->addRole(new ZendGenericRole(Acl::ROLE_USER), Acl::ROLE_GUEST)
            ->addRole(new ZendGenericRole(Acl::ROLE_MANAGER), Acl::ROLE_USER)
            ->addRole(new ZendGenericRole(Acl::ROLE_ADMIN), Acl::ROLE_MANAGER)
            ->addRole(new ZendGenericRole(Acl::ROLE_SUPER_ADMIN), Acl::ROLE_ADMIN);

        return $this;
    }
    /**
     * @return Service
     */
    private function addAcl()
    {
        $acl = $this->getAcl();
        $pathPattern = $this->getAclPath() . '*';
        foreach (ZendGlob::glob($pathPattern, ZendGlob::GLOB_BRACE) as $file) {
            /* @var \GeometriaLab\Acl\Resource $resource */
            $resourceName = '\\' . $this->moduleName . '\\' . self::ACL_DIR . '\\' . ucfirst(pathinfo($file, PATHINFO_FILENAME));
            $resourceId = $this->moduleName . '\\' . self::SERVICE_DIR . '\\' . ucfirst(pathinfo($file, PATHINFO_FILENAME));
            $resource = new $resourceName($resourceId);

            $acl->addResource($resource);

            $resource->createRoles($acl);
            $resource->createRules($acl);
            $resource->setCurrentRole($acl->getCurrentRole());
        }
        return $this;
    }
}
