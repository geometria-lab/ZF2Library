<?php

namespace GeometriaLab\Test\Helper;

interface HelperInterface
{
    /**
     * Set test case object
     *
     * @param \PHPUnit_Framework_Test $test
     * @return HelperInterface
     */
    public function setTest(\PHPUnit_Framework_Test $test);

    /**
     * Get test case object
     *
     * @return \PHPUnit_Framework_Test
     */
    public function getTest();
}
