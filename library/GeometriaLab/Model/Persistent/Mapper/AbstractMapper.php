<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use \Zend\Stdlib\Options as ZendOptions;

abstract class AbstractMapper extends ZendOptions implements MapperInterface
{
    /**
     * @var string
     */
    protected $modelClass;

    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    public function getModelClass()
    {
        return $this->modelClass;
    }
}
