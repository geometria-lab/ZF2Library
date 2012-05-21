<?php

namespace GeometriaLab\VBuckets;

use GeometriaLab\VBuckets\HashMethod\HashMethodInterface,
    GeometriaLab\VBuckets\Map\MapInterface;


class VBuckets
{
    /**
     * @var HashMethodInterface
     */
    protected $hashMethod;

    /**
     * @var MapInterface
     */
    protected $map;

    /**
     * Constructor
     *
     * @param HashMethodInterface $hash
     * @param MapInterface  $map
     */
    public function __construct(MapInterface $map, HashMethodInterface $hashMethod)
    {
        $this->map        = $map;
        $this->hashMethod = $hashMethod;
    }

    /**
     * Get vBucket by id
     *
     * @param  $id
     * @return VBucket
     */
    public function getById($id)
    {
        return $this->map->getVBucket($id);
    }

    /**
     * Get vBucket by key
     *
     * @param  $key
     * @return VBucket
     */
    public function getByKey($key)
    {
        $id = $this->hashMethod->getHash($key, $this->map);

        return $this->getById($id);
    }
}