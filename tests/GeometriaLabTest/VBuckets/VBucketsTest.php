<?php

namespace GeometriaLabTest\VBuckets;

use GeometriaLab\VBuckets\Map\Config,
    GeometriaLab\VBuckets\HashMethod\Modulo,
    GeometriaLab\VBuckets\VBuckets;

class GeometriaLab_VBucketsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VBuckets
     */
    protected $_vBuckets;

    public function setUp()
    {
        $config = new \Zend\Config\Config(array(
            'count' => 32768,
            'ranges' => array(
                16384 => array('range' => 1),
                32768 => array('range' => 2)
            )
        ));

        $map = new Config($config);

        $this->_vBuckets = new VBuckets($map, new Modulo());
    }

    public function testGetById()
    {
        $vBucket = $this->_vBuckets->getById(1231);
        $this->assertInstanceOf('\GeometriaLab\VBuckets\VBucket', $vBucket);
        $this->assertEquals(1231, $vBucket->id);
    }

    public function testGetByKey()
    {
        $vBucket = $this->_vBuckets->getByKey('as312321da1231sdada');
        $this->assertInstanceOf('\GeometriaLab\VBuckets\VBucket', $vBucket);
    }
}