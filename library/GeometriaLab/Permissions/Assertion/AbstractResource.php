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
     * Array of privileges which always allowed for all
     *
     * @var array
     */
    protected $allowedPrivileges = array();

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

    /**
     * Get array of privileges which always allowed for all
     *
     * @return array
     */
    public function getAllowedPrivileges()
    {
        return $this->allowedPrivileges;
    }
}
