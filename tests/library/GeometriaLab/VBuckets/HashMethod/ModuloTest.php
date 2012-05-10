<?php

class GeometriaLab_VBuckets_HashMethod_ModuloTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var GeometriaLab_VBuckets_HashMethod_Modulo
     */
    protected $_hashMethod;

    /**
     * @var GeometriaLab_VBuckets_Map_Config
     */
    protected $_map;

    public function setUp()
    {
        $this->_hashMethod = new GeometriaLab_VBuckets_HashMethod_Modulo();

        $config = new Zend_Config_Yaml(DATA_DIR . '/VBuckets/map.yaml');

        $this->_map = new GeometriaLab_VBuckets_Map_Config($config);
    }

    public function testGetHash()
    {
        $id = $this->_hashMethod->getHash('da1231312321312das', $this->_map);
        $this->assertEquals($id, $this->_hashMethod->getHash('dadas', $this->_map));

        $id2 = $this->_hashMethod->getHash(123133121, $this->_map);
        $this->assertNotEquals($id2, $this->_hashMethod->getHash('dadas', $this->_map));
    }
}