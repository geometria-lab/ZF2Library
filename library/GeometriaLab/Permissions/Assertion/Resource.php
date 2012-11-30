<?php

namespace GeometriaLab\Permissions\Assertion;

use GeometriaLab\Model\AbstractModel;

use Zend\ServiceManager\ServiceManager as ZendServiceManager,
    Zend\ServiceManager\ServiceManagerAwareInterface as ZendServiceManagerAwareInterface;

abstract class Resource implements ResourceInterface, ZendServiceManagerAwareInterface
{
    const DYNAMIC_ASSERT_PREFIX = 'can';

    /**
     * @var string
     */
    protected $name;
    /**
     * @var array
     */
    protected $privileges;
    /**
     * @var ZendServiceManager
     */
    protected $serviceManager;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;

        $prefixLength = strlen(self::DYNAMIC_ASSERT_PREFIX);
        $allMethods = get_class_methods($this);

        foreach ($allMethods as $methodName) {
            if (strpos($methodName, self::DYNAMIC_ASSERT_PREFIX) === 0) {
                $privilege = lcfirst(substr($methodName, $prefixLength));
                $this->addPrivilege($privilege);
            }
        }
    }

    /**
     * Defined by ResourceInterface; returns the Resource identifier
     * Proxies to getName()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Defined by ResourceInterface; returns the Resource identifier
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get all privileges defined for this resource
     *
     * @return array
     */
    public function getPrivileges()
    {
        return $this->privileges;
    }

    /**
     * Adds a privilege to the Resource
     *
     * @param string $privilege
     * @return Resource
     * @throws Exception\InvalidArgumentException
     */
    public function addPrivilege($privilege)
    {
        if ($this->hasPrivilege($privilege)) {
            throw new Exception\InvalidArgumentException("Privilege '$privilege' already exists in the Resource");
        }

        $this->privileges[$privilege] = $privilege;

        return $this;
    }

    /**
     * Returns true if and only if the Privilege exists in the Resource
     *
     * @param string $privilege
     * @return bool
     */
    public function hasPrivilege($privilege)
    {
        return isset($this->privileges[$privilege]);
    }

    /**
     * Set service manager
     *
     * @param ZendServiceManager $serviceManager
     */
    public function setServiceManager(ZendServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Get Service Manager
     *
     * @return ZendServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Dynamic assertion
     *
     * @param Assertion $assertion
     * @param string $privilege
     * @param AbstractModel $params
     * @return bool
     * @throws \InvalidArgumentException
     */
    public final function assert(Assertion $assertion, $privilege, AbstractModel $params = null)
    {
        $methodName = self::DYNAMIC_ASSERT_PREFIX . ucfirst($privilege);
        if (!method_exists($this, $methodName)) {
            throw new \InvalidArgumentException('Invalid dynamic assert - need declare ' . get_class($this) . '->' . $methodName);
        }

        return call_user_func(array($this, $methodName), $assertion, $params);
    }
}
