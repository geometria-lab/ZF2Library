<?php

class GeometriaLab_VBuckets_Map_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GeometriaLab_VBuckets_Map_Config
     */
    protected $_map;

    public function setUp()
    {
        $config = new Zend_Config_Yaml(DATA_DIR . '/VBuckets/map.yaml');

        $this->_map = new GeometriaLab_VBuckets_Map_Config($config);
    }

    /**
     * Get vBucket
     *
     * @param  $id
     * @return GeometriaLab_VBuckets_Bucket
     */
    public function testGetVBucket()
    {
        $bucket = $this->_map->getVBucket(3123);
        $this->assertInstanceOf('GeometriaLab_VBucket_Bucket', $bucket);
        $this->assertEquals(3123, $bucket->id);
        $this->assertEquals(1, $bucket->range);

        $bucket = $this->_map->getVBucket(32434);
        $this->assertInstanceOf('GeometriaLab_VBucket_Bucket', $bucket);
        $this->assertEquals(32434, $bucket->id);
        $this->assertEquals(2, $bucket->range);
    }


    public function testGetInvalidVBucket()
    {
        $this->setExpectedException('GeometriaLab_VBuckets_Map_Exception');
        $this->_map->getVBucket(66666);
    }

    public function getVBucketsCountTest()
    {
        $this->assertEquals(32768, $this->_map->getVBucketsCount());
    }
}
