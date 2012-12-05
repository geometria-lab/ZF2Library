<?php

namespace GeometriaLab\Permissions\Roles;

use GeometriaLab\Model\AbstractModel,
    GeometriaLab\Model\Persistent\AbstractModel as PersistentAbstractModel,
    GeometriaLab\Permissions\Assertion\Resource\ResourceInterface;

/**
 * @property \GeometriaLab\Permissions\Roles\ResourceRoles[]  $resourceRoles
 */
abstract class AbstractRoles extends PersistentAbstractModel
{
    /**
     * Resource roles map
     *
     * @var array
     */
    protected $resourceRolesMap;

    /**
     * Has role for Model
     *
     * @param string $role
     * @param AbstractModel $model
     * @return bool
     * @throws \RuntimeException
     */
    public function hasRole($role, AbstractModel $model)
    {
        if (!isset($model->id)) {
            $modelClassName = get_class($model);
            throw new \RuntimeException("Need 'id' property in model '{$modelClassName}'");
        }

        $parts = explode('\\', get_class($model));
        $resourceName = array_pop($parts);
        return $this->hasRoleForProperty('resourcesRoles', $role, $resourceName, $model->id);
    }

    /**
     * Has role for Resource in City
     *
     * @param string $role
     * @param string $cityId
     * @param ResourceInterface|string $resource
     * @return bool
     */
    public function hasRoleInCity($role, $cityId, $resource)
    {
        return $this->hasRoleForProperty('citiesRoles', $role, $resource, $cityId);
    }

    /**
     * Has Role for resource in property
     *
     * @param string $property
     * @param string $role
     * @param string $resourceName
     * @param string $objectId
     * @return bool
     * @throws \InvalidArgumentException
     */
    protected function hasRoleForProperty($property, $role, $resourceName, $objectId)
    {
        $permission = $this->getPermissionByResourceName($resourceName);

        if ($permission === null) {
            return false;
        }

        if (!isset($permission->{$property})) {
            throw new \InvalidArgumentException("Property '{$property}' doesn't exist");
        }

        // @TODO Hack for super manager
        if (isset($permission->{$property}[0]) && $permission->{$property}[0] === $role) {
            return true;
        }

        if (!isset($permission->{$property}[$objectId])) {
            return false;
        }

        return $permission->{$property}[$objectId] === $role;
    }

    /**
     * Get Role by name
     *
     * @param string $resourceName
     * @return ResourceRoles|null
     * @throws \RuntimeException
     */
    protected function getPermissionByResourceName($resourceName)
    {
        if ($this->resourceRoles === null) {
            return null;
        }

        if ($this->resourceRolesMap === null) {
            // @TODO Add feature getting Model from array by id
            foreach ($this->resourceRoles as $index => $resourceRole) {
                $this->resourceRolesMap[$resourceRole->resourceName] = $index;
            }
        }

        if (isset($this->resourceRolesMap[$resourceName])) {
            $index = $this->resourceRolesMap[$resourceName];
            if (!isset($this->resourceRoles[$index])) {
                throw new \RuntimeException("Can't find '{$resourceName}' resourceRoles. ResourceRolesMap is broken.");
            }

            return $this->resourceRoles[$index];
        }

        return null;
    }
}
