<?php

namespace GeometriaLab\Test;

interface TestCaseInterface
{
    /**
     * @param $helperBroker
     * @return TestCaseInterface
     */
    public function setHelperBroker($helperBroker);

    /**
     * @return \GeometriaLab\Test\Helper\HelperBroker
     */
    public function getHelperBroker();
}
