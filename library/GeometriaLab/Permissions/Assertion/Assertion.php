<?php

namespace GeometriaLab\Permissions\Assertion;

use GeometriaLab\Model\AbstractModel;

class Assertion
{
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
        $resourceId = (string) $resource;

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
        $resourceId = (string) $resource;

        if (!$this->hasResource($resource)) {
            throw new Exception\InvalidArgumentException("Resource '$resourceId' not found");
        }

        return $this->resources[$resourceId];
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
            $resourceId = (string) $resource;
            throw new Exception\InvalidArgumentException("Resource '$resourceId' not found");
        }

        $resourceId = $this->getResource($resource)->getName();

        unset($this->resources[$resourceId]);

        return $this;
    }

    /**
     * Returns false if and only if the Resource has deny to the $privilege
     *
     * @param ResourceInterface|string $resource
     * @param string $privilege
     * @param AbstractModel $params
     * @return bool
     */
    public function assert($resource, $privilege, AbstractModel $params = null)
    {
        if (!$this->hasResource($resource)) {
            return true;
        }

        $resource = $this->getResource($resource);

        if (!$resource->hasPrivilege($privilege)) {
            return true;
        }

        return $resource->assert($this, $privilege, $params);
    }
}
