<?php

namespace GeometriaLabTest\VBuckets\Map;

use GeometriaLab\VBuckets\Map\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $_map;

    public function setUp()
    {
        $config = new \Zend\Config\Config(array(
            'count' => 32768,
            'ranges' => array(
                16384 => array('range' => 1),
                32768 => array('range' => 2)
            )
        ));

        $this->_map = new Config($config);
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
        $this->assertInstanceOf('\GeometriaLab\VBuckets\VBucket', $bucket);
        $this->assertEquals(3123, $bucket->id);
        $this->assertEquals(1, $bucket->range);

        $bucket = $this->_map->getVBucket(32434);
        $this->assertInstanceOf('\GeometriaLab\VBuckets\VBucket', $bucket);
        $this->assertEquals(32434, $bucket->id);
        $this->assertEquals(2, $bucket->range);
    }


    public function testGetInvalidVBucket()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->_map->getVBucket(66666);
    }

    public function getVBucketsCountTest()
    {
        $this->assertEquals(32768, $this->_map->getVBucketsCount());
    }
}
