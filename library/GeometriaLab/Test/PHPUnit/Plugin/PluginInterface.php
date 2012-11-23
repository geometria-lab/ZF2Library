<?php

namespace GeometriaLab\Test\PHPUnit\Plugin;

use GeometriaLab\Test\PHPUnit\TestCaseInterface;

interface PluginInterface
{
    /**
     * Set test case object
     *
     * @param TestCaseInterface $test
     * @return PluginInterface
     */
    public function setTest(TestCaseInterface $test);

    /**
     * Get test case object
     *
     * @return TestCaseInterface
     */
    public function getTest();
}
