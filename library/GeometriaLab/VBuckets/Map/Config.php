<?php

class GeometriaLab_VBucket_Mapping_Config implements GeometriaLab_VBuckets_Map_Interface
{
    public function __construct(Zend_Config $config)
    {
        $this->_fillRanges($config);
    }

    public function getBucket($id)
    {
        $storageRanges = $this->getByPath("range", '.')->exportAssoc();
        return $this->_calculateDSN($storageRanges, $id);
    }

    public function getShardCount()
    {
        return $this->shardCount;
    }

    protected function _fillRanges(Zend_Config $Config) {
        foreach($Config as $storage => $storageConfig) {
            $this->_ensureStorageConfigIsWellFormatted($storage, $storageConfig);
            $this->setByPath(
            	"{$storage}.shardCount",
                (int)$storageConfig->shardCount,
                '.');
            $rangesMax = 0;
            foreach($storageConfig->range as $key => $range) {
                $this->_ensureRangeIsWellFormatted($storage, $key, $range);
                $this->setByPath(
                    "{$storage}.range.{$range->max}",
                    $range->dsn,
                    '.');
                if($range->max > $rangesMax) {
                    $rangesMax = $range->max;
                }
            }
            if($storageConfig->shardCount !== $rangesMax) {
                throw new GeometriaLabOld_LogicException(
                    "Shards are not covered by ranges; rengesMax: {$rangesMax}");
            }
        }
    }

    protected function _calculateDSN(array $ranges, $shardId) {
        $calculatedDsn = null;
        $calculatedMax = PHP_INT_MAX;
        foreach($ranges as $rangeMax => $dsn) {
            if($rangeMax >= $shardId && $rangeMax < $calculatedMax) {
                $calculatedMax = $rangeMax;
                $calculatedDsn = $dsn;
            }
        }
        if(null === $calculatedDsn) {
            throw new GeometriaLabOld_LogicException(
                "Cannot calculate DSN for shard#{$shardId}");
        }
        return $calculatedDsn . $shardId;
    }

    protected function _ensureStorageConfigIsWellFormatted($storage, $storageConfig) {
        $shardCountIsset = isset($storageConfig->shardCount);
        $rangeIsset = isset($storageConfig->range);
        if(false === $shardCountIsset) {
            throw new GeometriaLabOld_LogicException(
                "{$storage}.shardCount is undefined");
        }
        if(false === $rangeIsset) {
            throw new GeometriaLabOld_LogicException(
                "{$storage}.range is undefined");
        }
    }

    protected function _ensureRangeIsWellFormatted($storage, $key, $range) {
        $maxIsset = isset($range->max);
        $dsnIsset = isset($range->dsn);
        if(false === $maxIsset) {
            throw new GeometriaLabOld_LogicException(
                "{$storage}.range.{$key}.max is undefined");
        }
        if(false === $dsnIsset) {
            throw new GeometriaLabOld_LogicException(
                "{$storage}.range.{$key}.dsn is undefined");
        }
    }
}
