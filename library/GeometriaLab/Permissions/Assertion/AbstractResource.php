<?php

namespace GeometriaLab\Permissions\Assertion;

abstract class AbstractResource implements ResourceInterface
{
    /**
     * Resource unique identifier
     *
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
     * Get unique identifier
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
