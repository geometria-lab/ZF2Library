<?php

namespace GeometriaLab\Test\Helper;

abstract class AbstractHelper implements HelperInterface
{
    /**
     * @var \PHPUnit_Framework_Test
     */
    protected $test;

    /**
     * Set test object
     *
     * @param \PHPUnit_Framework_Test $test
     * @return AbstractHelper
     */
    public function setTest(\PHPUnit_Framework_Test $test)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Get test object
     *
     * @return \PHPUnit_Framework_Test
     */
    public function getTest()
    {
        return $this->test;
    }
}
