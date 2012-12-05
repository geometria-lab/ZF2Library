<?php

namespace GeometriaLab\Permissions\Assertion\Resource;

interface ResourceInterface
{
    /**
     * Get unique identifier
     *
     * @return string
     */
    public function getName();
}
