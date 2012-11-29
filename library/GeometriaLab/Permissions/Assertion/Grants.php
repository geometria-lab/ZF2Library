<?php

namespace GeometriaLab\Permissions\Assertion;

use GeometriaLab\Model\Persistent\AbstractModel;

/*
    userId: 123,
    resources: {
        "Venues":  {
            "cities": [1],
            "objects": [1, 2]
        }
    }
*/

/**
 * @property string $id         {"primary": true}
 * @property string $userId
 * @property array  $resources
 *
 * @method static \GeometriaLab\Mongo\Model\Mapper getMapper() {"mongoInstanceName" : "default", "collectionName" : "role"}
 */
class Grants extends AbstractModel
{
    /**
     * Grant Resource
     *
     * @param Resource $resource
     * @param string $objectId
     * @return bool
     */
    public function grantResource(Resource $resource, $objectId)
    {
        return $this->grantForStructure($resource, $objectId, 'objects');
    }

    /**
     * Grant Resource in City
     *
     * @param Resource $resource
     * @param string $cityId
     * @return bool
     */
    public function grantCity(Resource $resource, $cityId)
    {
        return $this->grantForStructure($resource, $cityId, 'city');
    }

    /**
     * Grant for structure
     *
     * @param Resource $resource
     * @param string $id
     * @param string $structureName
     * @return bool
     */
    protected function grantForStructure(Resource $resource, $id, $structureName)
    {
        $resourceId = $resource->getId();

        if (!isset($this->resources[$resourceId])) {
            return false;
        }

        if (isset($this->resources[$resourceId][$structureName]) && empty($this->resources[$resourceId][$structureName])) {
            return true;
        }

        return isset($this->resources[$resourceId][$structureName][$id]);
    }
}
