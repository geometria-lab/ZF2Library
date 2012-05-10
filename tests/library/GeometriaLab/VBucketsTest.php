<?php

class GeometriaLab_VBucketsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GeometriaLab_VBuckets
     */
    protected $_vBuckets;

    public function setUp()
    {
        $config = new Zend_Config_Yaml(DATA_DIR . '/VBuckets/map.yaml');

        $map = new GeometriaLab_VBuckets_Map_Config($config);

        $this->_vBuckets = new GeometriaLab_VBuckets($map, new GeometriaLab_VBuckets_HashMethod_Modulo());
    }

    public function testGetById()
    {
        $vBucket = $this->_vBuckets->getById(1231);
        $this->assertInstanceOf('GeometriaLab_VBuckets_Bucket', $vBucket);
        $this->assertEquals(1231, $vBucket->id);
    }

    public function testGetByKey()
    {
        $vBucket = $this->_vBuckets->getByKey('as312321da1231sdada');
        $this->assertInstanceOf('GeometriaLab_VBuckets_Bucket', $vBucket);
    }
}