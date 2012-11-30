<?php

namespace GeometriaLab\Test\PHPUnit\Controller;

/**
 * @method \Zend\Http\PhpEnvironment\Request getRequest()
 * @method \Zend\Http\PhpEnvironment\Response getResponse()
 */
abstract class AbstractHttpControllerTestCase extends AbstractControllerTestCase
{
    protected $useConsoleRequest = false;

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
        $message = ($message === '') ?: sprintf('Failed asserting that JSON path "%s" value equals "%s"', $path, $expected);

        $actual = $this->getJsonValueByPath($path);

        self::assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     *  Assert that content-type header and $expected are equals
     *
     * @param string $expected
     * @param string $message
     * @param int $delta
     * @param int $maxDepth
     * @param bool $canonicalize
     * @param bool $ignoreCase
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    protected function assertContentType($expected, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        $contentType = $this->getResponse()->getHeaders()->get('content-type');

        if ($contentType === false) {
            throw new \PHPUnit_Framework_AssertionFailedError('Content-type not found in Headers');
        }

        self::assertEquals($expected, $contentType->getFieldValue(), $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * Get JSON value by path
     *
     * @param string $path
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @return mixed
     */
    protected function getJsonValueByPath($path = '')
    {
        $json = $this->getJsonFromResponse();

        if (!is_array($json)) {
            throw new \PHPUnit_Framework_AssertionFailedError('Path not found in JSON');
        }

        $path = trim($path, '/');

        if ($path === '') {
            return $json;
        }

        $sections = explode('/', $path);

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
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    protected function getJsonFromResponse()
    {
        $contentType = $this->getResponse()->getHeaders()->get('content-type');

        if ($contentType === false) {
            throw new \PHPUnit_Framework_AssertionFailedError('Content-type not found in Headers');
        }

        if ($contentType->getFieldValue() !== 'application/json') {
            throw new \PHPUnit_Framework_AssertionFailedError('Content-type not equals "application/json"');
        }

        return json_decode($this->getResponse()->getContent(), true);
    }
}
