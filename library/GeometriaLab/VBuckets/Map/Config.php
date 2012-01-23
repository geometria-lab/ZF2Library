<?php

class GeometriaLab_VBucket_Map_Config implements GeometriaLab_VBuckets_Map_Interface
{
    /**
     * Config
     *
     * @var \Zend_Config
     */
    protected $_config;

    /**
     * Constructor
     *
     * @param Zend_Config $config
     */
    public function __construct(Zend_Config $config)
    {
        $this->_config = $this->_validate($config);
    }

    /**
     * Get vBucket
     *
     * @param  $id
     * @return GeometriaLab_VBuckets_Bucket
     */
    public function getVBucket($id)
    {
        foreach($this->_getConfig()->ranges as $maxId => $range) {
            if ($maxId <= $id) {
                return new GeometriaLab_VBuckets_Bucket($id, $range);
            }
        }

        throw new GeometriaLab_VBucket_Map_Exception('Can\'t get vBucket from ranges');
    }

    /**
     * Get sharding count
     *
     * @return integer
     */
    public function getVBucketsCount()
    {
        return $this->_getConfig()->count;
    }

    /**
     * Validate config
     *
     * @throws GeometriaLab_VBuckets_Map_Exception
     * @param Zend_Config $config
     * @return Zend_Config
     */
    protected function _validate(Zend_Config $config)
    {
        if (!isset($config->count) ||
            (integer)$config->count != $config->count ||
            (integer)$config->count < 1) {
            throw new GeometriaLab_VBuckets_Map_Exception('Undefined vBuckets count');
        }

        $config->count = (integer)$config->count;

        if (!isset($config->ranges) || count($config->ranges) == 0) {
            throw new GeometriaLab_VBuckets_Map_Exception('Ranges not present');
        }

        foreach($config->ranges as $maxId => $range) {
            if (!is_int($maxId) || $maxId < 1 || $maxId > $config->count) {
                throw new GeometriaLab_VBuckets_Map_Exception('Invalid range');
            }
        }

        if (!isset($config->ranges[$config->count])) {
            throw new GeometriaLab_VBuckets_Map_Exception('vBuckets count are not covered by ranges');
        }

        return $config;
    }

    /**
     * Get config
     *
     * @return Zend_Config
     */
    protected function _getConfig()
    {
        return $this->_config;
    }
}
