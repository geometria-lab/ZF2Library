<?php

namespace GeometriaLabTest\Permissions\Acl\Sample;

use GeometriaLab\Permissions\Acl\Resource;

use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\GenericRole as ZendGenericRole,
    Zend\Permissions\Acl\Role\RoleInterface as ZendRoleInterface;

class Users extends Resource
{
    public function createRoles(ZendAcl $acl)
    {
        $acl->addRole(new ZendGenericRole('moderator'), 'user');
    }

    public function createRules(ZendAcl $acl)
    {
        $acl->allow('moderator', $this, 'assert');
        $acl->allow('moderator', $this, 'dynamic', $this);
        $acl->allow('moderator', $this, 'notDynamic', $this);
    }

    protected function assertDynamic(ZendAcl $acl, ZendRoleInterface $role)
    {
        return true;
    }
}
