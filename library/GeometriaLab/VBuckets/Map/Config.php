<?php

namespace GeometriaLab\VBuckets\Map;

use GeometriaLab\VBuckets\Bucket;

use Zend\Config\Config as ZendConfig;

class Config implements MapInterface
{
    /**
     * Config
     *
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param ZendConfig $config
     */
    public function __construct(ZendConfig $config)
    {
        $this->config = $this->convertToArray($config);
    }

    /**
     * Get vBucket
     *
     * @param $id
     * @return Bucket
     * @throws \Exception
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
     * Get vBuckets count
     *
     * @return integer
     */
    public function getVBucketsCount()
    {
        return $this->_config['count'];
    }

    /**
     * Convert and validate Zend\Config to array
     *
     * @throws \Exception
     * @param ZendConfig $config
     * @return array
     */
    protected function convertToArray(ZendConfig $config)
    {
        $configArray = $config->toArray();

        if (!isset($configArray['count'])) {
            throw new \Exception('Undefined vBuckets count');
        }

        $configArray['count'] = (integer)$configArray['count'];

        if ($configArray['count'] < 1) {
            throw new \Exception('vBuckets count must be positive integer');
        }

        if (!isset($configArray['ranges']) || count($configArray['ranges']) == 0) {
            throw new \Exception('Ranges not present');
        }

        ksort($configArray['ranges']);

        $lastRangeMaxId = 0;
        foreach($configArray['ranges'] as $maxId => $range) {
            if (!is_int($maxId) || $maxId < 1 || $maxId > $config['count']) {
                throw new \Exception('Invalid range');
            }
            $lastRangeMaxId = $maxId;
        }

        if ($lastRangeMaxId != $configArray['count']) {
            throw new \Exception('vBuckets count are not covered by ranges');
        }

        return $configArray;
    }
}
