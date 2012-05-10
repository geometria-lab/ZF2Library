<?php

class GeometriaLab_VBuckets_Map_Config implements GeometriaLab_VBuckets_Map_Interface
{
    /**
     * Config
     *
     * @var array
     */
    protected $_config;

    /**
     * Constructor
     *
     * @param array|Zend_Config $config
     */
    public function __construct($config)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        $this->_config = $this->_validate($config);
    }

    /**
     * Get vBucket
     *
     * @param $id
     * @return GeometriaLab_VBuckets_Bucket
     * @throws GeometriaLab_VBuckets_Map_Exception
     */
    public function getVBucket($id)
    {
        foreach($this->_config['ranges'] as $maxId => $range) {
            if ($maxId >= $id) {
                return new GeometriaLab_VBuckets_Bucket($id, $range);
            }
        }

        throw new GeometriaLab_VBuckets_Map_Exception("Invalid vBucket id $id");
    }

    /**
     * Get sharding count
     *
     * @return integer
     */
    public function getVBucketsCount()
    {
        return $this->_config['count'];
    }

    /**
     * Validate config
     *
     * @throws GeometriaLab_VBuckets_Map_Exception
     * @param array $config
     * @return array
     */
    protected function _validate($config)
    {
        if (!isset($config['count']) ||
            (integer)$config['count'] != $config['count'] ||
            (integer)$config['count'] < 1) {
            throw new GeometriaLab_VBuckets_Map_Exception('Undefined vBuckets count');
        }

        $config['count'] = (integer)$config['count'];

        if (!isset($config['ranges']) || count($config['ranges']) == 0) {
            throw new GeometriaLab_VBuckets_Map_Exception('Ranges not present');
        }

        ksort($config['ranges']);

        $lastRangeMaxId = 0;
        foreach($config['ranges'] as $maxId => $range) {
            if (!is_int($maxId) || $maxId < 1 || $maxId > $config['count']) {
                throw new GeometriaLab_VBuckets_Map_Exception('Invalid range');
            }
            $lastRangeMaxId = $maxId;
        }

        if ($lastRangeMaxId != $config['count']) {
            throw new GeometriaLab_VBuckets_Map_Exception('vBuckets count are not covered by ranges');
        }

        return $config;
    }
}
