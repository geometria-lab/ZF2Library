<?php

namespace GeometriaLab\Model\Persistent\Schema;

class Schema extends \GeometriaLab\Model\Schema\Schema
{
    /**
     * Expected properties interface
     *
     * @var array
     */
    static protected $propertyInterface = 'GeometriaLab\Model\Persistent\Schema\Property\PropertyInterface';

    /**
     * Mapper class
     *
     * @var string
     */
    protected $mapperClass;

    /**
     * Mapper params
     *
     * @var array
     */
    protected $mapperOptions = array();

    /**
     * Set mapper class
     *
     * @param string $mapperClass
     */
    public function setMapperClass($mapperClass)
    {
        $this->mapperClass = $mapperClass;
    }

    /**
     * Get mapper class
     *
     * @return string
     */
    public function getMapperClass()
    {
        return $this->mapperClass;
    }

    /**
     * Set mapper params
     *
     * @param array $mapperOptions
     */
    public function setMapperOptions($mapperOptions)
    {
        $this->mapperOptions = $mapperOptions;
    }

    /**
     * Get mapper
     *
     * @return array
     */
    public function getMapperOptions()
    {
        return $this->mapperOptions;
    }
}