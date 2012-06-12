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
}