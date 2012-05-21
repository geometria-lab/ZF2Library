<?php

namespace GeometriaLabTest\VBuckets\HashMethod;

use GeometriaLab\VBuckets\HashMethod\Modulo,
    GeometriaLab\VBuckets\Map\Config;

class ModuloTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Modulo
     */
    protected $_hashMethod;

    /**
     * @var Config
     */
    protected $_map;

    public function setUp()
    {
        $this->_hashMethod = new Modulo();

        $config = new \Zend\Config\Config(array(
            'count' => 32768,
            'ranges' => array(
                16384 => array('range' => 1),
                32768 => array('range' => 2)
            )
        ));

        $this->_map = new Config($config);
    }

    public function testGetHash()
    {
        $id = $this->_hashMethod->getHash('da1231312321312das', $this->_map);
        $this->assertEquals($id, $this->_hashMethod->getHash('dadas', $this->_map));

        $id2 = $this->_hashMethod->getHash(123133121, $this->_map);
        $this->assertNotEquals($id2, $this->_hashMethod->getHash('dadas', $this->_map));
    }
}