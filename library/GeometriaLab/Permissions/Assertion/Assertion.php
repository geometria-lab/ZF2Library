<?php

namespace GeometriaLab\Permissions\Assertion;

use GeometriaLab\Model\AbstractModel;

class Assertion
{
    const DYNAMIC_ASSERT_PREFIX = 'can';

    /**
     * Resource tree
     *
     * @var ResourceInterface[]
     */
    protected $resources = array();

    /**
     * Get all resources
     *
     * @return ResourceInterface[]
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Adds a Resource having an identifier unique to the Assertion
     *
     * @param ResourceInterface $resource
     * @throws Exception\InvalidArgumentException
     * @return Assertion
     */
    public function addResource(ResourceInterface $resource)
    {
        $resourceName = $resource->getName();

        if ($this->hasResource($resourceName)) {
            throw new Exception\InvalidArgumentException("Resource id '$resourceName' already exists in the Assertion");
        }

        $this->resources[$resourceName] = $resource;

        return $this;
    }

    /**
     * Returns true if and only if the Resource exists in the Assertion
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param ResourceInterface|string $resource
     * @return boolean
     */
    public function hasResource($resource)
    {
        if ($resource instanceof ResourceInterface) {
            $resourceId = $resource->getName();
        } else {
            $resourceId = (string) $resource;
        }

        return isset($this->resources[$resourceId]);
    }

    /**
     * Returns the identified Resource
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param ResourceInterface|string $resource
     * @throws Exception\InvalidArgumentException
     * @return ResourceInterface
     */
    public function getResource($resource)
    {
        if ($resource instanceof ResourceInterface) {
            $resourceName = $resource->getName();
        } else {
            $resourceName = $resource;
        }

        if (!$this->hasResource($resource)) {
            throw new Exception\InvalidArgumentException("Resource '$resourceName' not found");
        }

        return $this->resources[$resourceName];
    }

    /**
     * Removes a Resource and all of its children
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param ResourceInterface|string $resource
     * @throws Exception\InvalidArgumentException
     * @return Assertion
     */
    public function removeResource($resource)
    {
        if (!$this->hasResource($resource)) {
            if ($resource instanceof ResourceInterface) {
                $resourceName = $resource->getName();
            } else {
                $resourceName = $resource;
            }
            throw new Exception\InvalidArgumentException("Resource '$resourceName' not found");
        }

        $resourceName = $this->getResource($resource)->getName();

        unset($this->resources[$resourceName]);

        return $this;
    }

    /**
     * Returns false if and only if the Resource has deny to the $privilege
     *
     * @param ResourceInterface|string $resource
     * @param string $privilege
     * @param mixed $arg1 [optional]
     * @param mixed $arg2 [optional]
     * @param mixed $argN [optional]
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function assert($resource, $privilege, $arg1 = null, $arg2 = null, $argN = null)
    {
        if (!$this->hasResource($resource)) {
            return false;
        }

        $resource = $this->getResource($resource);
        $methodName = self::DYNAMIC_ASSERT_PREFIX . ucfirst($privilege);

        if (!method_exists($resource, $methodName)) {
            throw new Exception\InvalidArgumentException('Need declare ' . get_class($resource) . '->' . $methodName);
        }

        $funcArgs = func_get_args();
        // Remove $resource and $privilege from array
        unset($funcArgs[0], $funcArgs[1]);
        // Assertion must be a first
        array_unshift($funcArgs, $this);

        return call_user_func_array(array($resource, $methodName), $funcArgs);
    }
}
