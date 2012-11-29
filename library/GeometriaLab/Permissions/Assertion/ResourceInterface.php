<?php

namespace GeometriaLab\Permissions\Assertion;

use GeometriaLab\Model\AbstractModel;

interface ResourceInterface
{
    /**
     * Get unique identifier
     *
     * @return string
     */
    public function getId();

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
     * @param AbstractModel $params
     * @return bool
     */
    public function assert(Assertion $assertion, $privilege, AbstractModel $params = null);
}
