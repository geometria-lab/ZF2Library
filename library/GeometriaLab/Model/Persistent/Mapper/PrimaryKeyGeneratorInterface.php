<?php

namespace GeometriaLab\Model\Persistent\Mapper;

interface PrimaryKeyGeneratorInterface
{
    /**
     * Get next id
     *
     * @abstract
     * @return mixed
     */
    public function generate();

    /**
     * Set mapper
     *
     * @abstract
     * @param MapperInterface $mapper
     * @return mixed
     */
    public function setMapper(MapperInterface $mapper);

    /**
     * Get mapper
     *
     * @abstract
     * @return mixed
     */
    public function getMapper();
}