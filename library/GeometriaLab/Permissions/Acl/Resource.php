<?php

namespace GeometriaLab\Permissions\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\RoleInterface as ZendRoleInterface,
    Zend\Permissions\Acl\Resource\ResourceInterface as ZendResourceInterface,
    Zend\Permissions\Acl\Resource\GenericResource as ZendResource,
    Zend\Permissions\Acl\Assertion\AssertionInterface as ZendAssertionInterface;

abstract class Resource extends ZendResource implements ZendAssertionInterface
{
    /**
     * @param ZendAcl $acl
     */
    public function createRoles(ZendAcl $acl) { }
    /**
     * @abstract
     * @param ZendAcl $acl
     */
    abstract public function createRules(ZendAcl $acl);
    /**
     * @param ZendAcl $acl
     * @param ZendRoleInterface $role
     * @param ZendResourceInterface $resource
     * @param string $privilege
     * @return bool
     * @throws \InvalidArgumentException
     */
    public final function assert(ZendAcl $acl, ZendRoleInterface $role = null, ZendResourceInterface $resource = null, $privilege = null)
    {
        if (!$privilege) {
            return false;
        }

        $methodName = "assert{$privilege}";
        if (!method_exists($this, $methodName)) {
            throw new \InvalidArgumentException("Invalid dynamic assert - need declare $this->$methodName");
        }

        return call_user_func(array($this, $methodName), $acl, $role);
    }
}
