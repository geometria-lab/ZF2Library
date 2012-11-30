<?php

namespace GeometriaLab\Permissions\Assertion;

use GeometriaLab\Model\Schemaless\Model;

interface ResourceInterface
{
    /**
     * Get unique identifier
     *
     * @return string
     */
    public function getName();

    /**
     * Returns true if and only if the Privilege exists in the Resource
     *
     * @param $privilege
     * @return bool
     */
    public function hasPrivilege($privilege);

    /**
     * Dynamic assertion
     *
     * @param Assertion $assertion
     * @param string $privilege
     * @param Model $model
     * @return bool
     */
    public function assert(Assertion $assertion, $privilege, Model $model = null);
}
