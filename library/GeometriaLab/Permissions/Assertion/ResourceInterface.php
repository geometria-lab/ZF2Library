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
}
