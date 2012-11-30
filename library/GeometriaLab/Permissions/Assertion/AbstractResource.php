<?php

namespace GeometriaLab\Permissions\Assertion;

use Zend\ServiceManager\ServiceManager as ZendServiceManager;

abstract class AbstractResource implements ResourceInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;
    }

    /**
     * Defined by ResourceInterface; returns the Resource identifier
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
