<?php

namespace GeometriaLab\Acl;

use Zend\Permissions\Acl\Resource\GenericResource as ZendResource,
    Zend\Permissions\Acl\Assertion\AssertionInterface as ZendAssertionInterface,
    Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\RoleInterface as ZendRoleInterface,
    Zend\Permissions\Acl\Resource\ResourceInterface as ZendResourceInterface;

abstract class Resource extends ZendResource implements ZendAssertionInterface
{
    /**
     * @var string
     */
    private $currentRole;

    /**
     * @param Acl $acl
     */
    public function createRoles(Acl $acl) { }
    /**
     * @abstract
     * @param Acl $acl
     * @return mixed
     */
    abstract public function createRules(Acl $acl);
    /**
     * @param string $role
     * @return Resource
     */
    public final function setCurrentRole($role)
    {
        $this->currentRole = $role;
        return $this;
    }
    /**
     * @return string
     */
    public final function getCurrentRole()
    {
        return $this->currentRole;
    }
    /**
     * @param ZendAcl $acl
     * @param ZendRoleInterface $role
     * @param ZendResourceInterface $resource
     * @param null $privilege
     * @return bool|void
     */
    public final function assert(ZendAcl $acl, ZendRoleInterface $role = null, ZendResourceInterface $resource = null, $privilege = null)
    {
        if (!$privilege) {
            return false;
        }

        $methodName = "assert{$privilege}";
        if (method_exists($this, $methodName)) {
            return call_user_func(array($this, $methodName), $acl, $this->getCurrentRole());
        }

        return false;
    }
}
