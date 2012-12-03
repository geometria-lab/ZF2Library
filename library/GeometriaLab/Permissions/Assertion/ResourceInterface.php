<?php

namespace GeometriaLab\Permissions\Assertion;

interface ResourceInterface
{
    /**
     * Get unique identifier
     *
     * @return string
     */
    public function getName();

    /**
     * Get array of privileges which always allowed for all
     *
     * @return array
     */
    public function getAllowedPrivileges();
}
