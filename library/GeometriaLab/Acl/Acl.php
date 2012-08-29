<?php

namespace GeometriaLab\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Resource\ResourceInterface as ZendResourceInterface,
    Zend\Permissions\Acl\Role\RoleInterface as ZendRoleInterface,
    GeometriaLab\Acl\Resource;

class Acl extends ZendAcl
{
    const ROLE_GUEST = 'guest';
    const ROLE_USER = 'user';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPER_ADMIN = 'super_admin';

    /**
     * @var string
     */
    private $currentRole = self::ROLE_GUEST;
    /**
     * @var string|Resource
     */
    private $currentResource;
    /**
     * @var string
     */
    private $currentPrivilege;

    /**
     * @param $role
     * @return Acl
     */
    public function setCurrentRole($role)
    {
        $this->currentRole = $role;
        return $this;
    }
    /**
     * @return string
     */
    public function getCurrentRole()
    {
        return $this->currentRole;
    }
    /**
     * @param string|Resource $resource
     * @return Acl
     */
    public function setCurrentResource($resource)
    {
        $this->currentResource = $resource;
        return $this;
    }
    /**
     * @return string|Resource
     */
    public function getCurrentResource()
    {
        return $this->currentResource;
    }
    /**
     * @param string $privilege
     * @return Acl
     */
    public function setCurrentPrivilege($privilege)
    {
        $this->currentPrivilege = $privilege;
        return $this;
    }
    /**
     * @return string
     */
    public function getCurrentPrivilege()
    {
        return $this->currentPrivilege;
    }
    /**
     * @param null|string|ZendRoleInterface $role
     * @param null|string|Resource $resource
     * @param null|string $privilege
     * @return bool
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        if ($role === null) {
            $role = $this->getCurrentRole();
        }
        if ($resource === null) {
            $resource = $this->getCurrentResource();
        }
        if ($resource === null) {
            $resource = $this->getCurrentResource();
        }
        if ($privilege === null) {
            $privilege = $this->getCurrentPrivilege();
        }
        if (!$this->hasRole($role) || !$this->hasResource($resource)) {
            return false;
        }

        $result = parent::isAllowed($role, $resource, $privilege);

        return $result;
    }
}
