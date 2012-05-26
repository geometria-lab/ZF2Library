<?php

namespace GeometriaLab\Model\Persistent\Mapper;

class Manager implements \IteratorAggregate
{
    protected $mappers;

    protected static $instance;

    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self;
        }

        return static::$instance;
    }

    public function get($modelClass)
    {
        
    }

    public function has($modelClass)
    {

    }
}
