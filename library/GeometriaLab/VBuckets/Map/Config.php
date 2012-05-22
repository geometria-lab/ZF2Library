<?php

namespace GeometriaLab\VBuckets\Map;

use GeometriaLab\VBuckets\VBucket;

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
     * @return VBucket
     * @throws \InvalidArgumentException
     */
    public function getVBucket($id)
    {
        foreach($this->config['ranges'] as $maxId => $range) {
            if ($maxId >= $id) {
                return new VBucket($id, $range);
            }
        }

        throw new \InvalidArgumentException("Invalid vBucket id $id");
    }

    /**
     * Get vBuckets count
     *
     * @return integer
     */
    public function getVBucketsCount()
    {
        return $this->config['count'];
    }

    /**
     * Convert and validate Zend\Config to array
     *
     * @throws \InvalidArgumentException
     * @param ZendConfig $config
     * @return array
     */
    protected function convertToArray(ZendConfig $config)
    {
        $configArray = $config->toArray();

        if (!isset($configArray['count'])) {
            throw new \InvalidArgumentException('Undefined vBuckets count');
        }

        $configArray['count'] = (integer)$configArray['count'];

        if ($configArray['count'] < 1) {
            throw new \InvalidArgumentException('vBuckets count must be positive integer');
        }

        if (!isset($configArray['ranges']) || count($configArray['ranges']) == 0) {
            throw new \InvalidArgumentException('Ranges not present');
        }

        ksort($configArray['ranges']);

        $lastRangeMaxId = 0;
        foreach($configArray['ranges'] as $maxId => $range) {
            if (!is_int($maxId) || $maxId < 1 || $maxId > $config['count']) {
                throw new \InvalidArgumentException('Invalid range');
            }
            $lastRangeMaxId = $maxId;
        }

        if ($lastRangeMaxId != $configArray['count']) {
            throw new \InvalidArgumentException('vBuckets count are not covered by ranges');
        }

        return $configArray;
    }
}
