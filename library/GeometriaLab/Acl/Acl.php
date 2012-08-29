<?php

namespace GeometriaLab\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Resource\ResourceInterface as ZendResourceInterface,
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
     * @param null|string|ZendRoleInterface $role
     * @param null|string|Resource $resource
     * @param null|string $privilege
     * @return bool
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        if (empty($role)) {
            $role = $this->getCurrentRole();
        }

        if (!$this->hasRole($role) || !$this->hasResource($resource)) {
            return false;
        }

        $result = parent::isAllowed($role, $resource, $privilege);

        return $result;
    }
}
