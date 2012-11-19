<?php

namespace GeometriaLab\Test\PHPUnit\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase as ZendAbstractHttpControllerTestCase;

class AbstractHttpControllerTestCase extends ZendAbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include 'config/application.config.php'// Use relative path
        );

        // @TODO Hack
        $this->getApplication();

        parent::setUp();
    }

    /**
     * Assert that JSON piece by path $path and $expected are equals
     *
     * @param string $path
     * @param mixed $expected
     * @param string $message
     * @param int $delta
     * @param int $maxDepth
     * @param bool $canonicalize
     * @param bool $ignoreCase
     */
    public function assertJsonEquals($path, $expected, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        $message = ($message) ?: sprintf('Failed asserting that JSON path "%s" value equals "%s"', $path, $expected);

        $actual = $this->getJsonValueByPath($path);

        self::assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * Get JSON value by path
     *
     * @param string $path
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @return mixed
     */
    protected function getJsonValueByPath($path)
    {
        $path = trim($path, '/');
        $sections = explode('/', $path);
        $json = $this->getJsonFromResponse();

        if (!is_array($json)) {
            throw new \PHPUnit_Framework_AssertionFailedError('Path not found in JSON');
        }

        foreach ($sections as $section) {
            if (array_key_exists($section, $json)) {
                $json = $json[$section];
            } else {
                throw new \PHPUnit_Framework_AssertionFailedError('Path not found in JSON');
            }
        }

        return $json;
    }

    /**
     * Get JSON from Response
     *
     * @return array
     */
    protected function getJsonFromResponse()
    {
        return json_decode($this->getResponse()->getContent(), true);
    }
}
