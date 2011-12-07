<?php

/**
 * @author ivanshumkov
 */
class GeometriaLab_VBuckets
{
    /**
     * @var \GeometriaLab_VBuckets_Hash_Interface
     */
    protected $_hash;

    /**
     * @var \GeometriaLab_VBuckets_Map_Interface
     */
    protected $_map;

    /**
     * Constructor
     *
     * @param GeometriaLab_VBuckets_Hash_Interface $hash
     * @param GeometriaLab_VBuckets_Map_Interface  $map
     */
    public function __construct(GeometriaLab_VBuckets_Hash_Interface $hash, GeometriaLab_VBuckets_Map_Interface $map)
    {
        $this->_map  = $map;
        $this->_hash = $hash;
    }

    protected function getById($id)
    {
        return $this->_getMap()->getVBucket($id);
    }

    protected function getByKey($key)
    {
        $id = $this->_getHash()->getHash($key, $this->_getMap());

        return $this->getById($id);
    }

    protected function _getMap()
    {
        return $this->_map;
    }

    protected function _getHash()
    {
        return $this->_hash;
    }
}