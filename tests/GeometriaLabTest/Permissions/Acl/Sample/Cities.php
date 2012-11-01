<?php

namespace GeometriaLabTest\Permissions\Acl\Sample;

use GeometriaLab\Permissions\Acl\Resource;

use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\GenericRole as ZendGenericRole;

class Cities extends Resource
{
    public function createRoles(ZendAcl $acl)
    {
        $acl->addRole(new ZendGenericRole('cityManager'));
    }

    public function createRules(ZendAcl $acl)
    {
        $acl->allow('cityManager', $this, 'assert');
    }
}
