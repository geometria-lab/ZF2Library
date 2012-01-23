<?php

/**
 * @author ivanshumkov
 */
class GeometriaLab_VBuckets
{
    /**
     * @var \GeometriaLab_VBuckets_HashMethod_Interface
     */
    protected $_hashMethod;

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
    public function __construct(GeometriaLab_VBuckets_Map_Interface $map, GeometriaLab_VBuckets_HashMethod_Interface $hashMethod)
    {
        $this->_map        = $map;
        $this->_hashMethod = $hashMethod;
    }

    /**
     * Get vBucket by id
     *
     * @param  $id
     * @return GeometriaLab_VBuckets_Bucket
     */
    public function getById($id)
    {
        return $this->_getMap()->getVBucket($id);
    }

    /**
     * Get vBucket by key
     *
     * @param  $key
     * @return GeometriaLab_VBuckets_Bucket
     */
    public function getByKey($key)
    {
        $id = $this->_getHashMethod()->getHash($key, $this->_getMap());

        return $this->getById($id);
    }

    /**
     * Get map
     *
     * @return GeometriaLab_VBuckets_Map_Interface
     */
    protected function _getMap()
    {
        return $this->_map;
    }

    /**
     * Get hash method
     *
     * @return GeometriaLab_VBuckets_HashMethod_Interface
     */
    protected function _getHashMethod()
    {
        return $this->_hashMethod;
    }
}