<?php

namespace GeometriaLab\Permissions\Assertion;

class Assertion
{
    const DYNAMIC_ASSERT_PREFIX = 'can';

    /**
     * Resource tree
     *
     * @var Resource\ResourceInterface[]
     */
    protected $resources = array();

    /**
     * Get all resources
     *
     * @return Resource\ResourceInterface[]
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Adds a Resource having an identifier unique to the Assertion
     *
     * @param Resource\ResourceInterface $resource
     * @throws Exception\InvalidArgumentException
     * @return Assertion
     */
    public function addResource(Resource\ResourceInterface $resource)
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
     * @param Resource\ResourceInterface|string $resource
     * @return boolean
     */
    public function hasResource($resource)
    {
        $resourceName = self::getResourceName($resource);

        return isset($this->resources[$resourceName]);
    }

    /**
     * Returns the identified Resource
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param Resource\ResourceInterface|string $resource
     * @throws Exception\InvalidArgumentException
     * @return Resource\ResourceInterface
     */
    public function getResource($resource)
    {
        $resourceName = self::getResourceName($resource);

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
     * @param Resource\ResourceInterface|string $resource
     * @throws Exception\InvalidArgumentException
     * @return Assertion
     */
    public function removeResource($resource)
    {
        $resourceName = self::getResourceName($resource);

        if (!$this->hasResource($resource)) {
            throw new Exception\InvalidArgumentException("Resource '$resourceName' not found");
        }

        unset($this->resources[$resourceName]);

        return $this;
    }

    /**
     * Returns false if and only if the Resource has deny to the $privilege
     *
     * @param Resource\ResourceInterface|string $resource
     * @param string $privilege
     * @param mixed $arg1 [optional]
     * @param mixed $arg2 [optional]
     * @param mixed $argN [optional]
     * @return bool
     * @throws Exception\RuntimeException
     */
    public function assert($resource, $privilege, $arg1 = null, $arg2 = null, $argN = null)
    {
        $resource = $this->getResource($resource);

        $methodName = self::DYNAMIC_ASSERT_PREFIX . ucfirst($privilege);
        if (!method_exists($resource, $methodName)) {
            throw new Exception\RuntimeException("No rules for privilege '{$privilege}'");
        }

        $funcArgs = func_get_args();
        // Remove $resource and $privilege from array
        unset($funcArgs[0], $funcArgs[1]);
        // Assertion must be a first
        array_unshift($funcArgs, $this);

        return call_user_func_array(array($resource, $methodName), $funcArgs);
    }

    /**
     * Get Resource name
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param Resource\ResourceInterface|string $resource
     * @return string
     */
    protected static function getResourceName($resource)
    {
        if ($resource instanceof Resource\ResourceInterface) {
            return $resource->getName();
        }

        return (string) $resource;
    }
}
