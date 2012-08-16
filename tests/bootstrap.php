<?php

/*
 * Set error reporting to the level
 */
error_reporting( E_ALL | E_STRICT );
ini_set('display_errors', true);

$phpUnitVersion = PHPUnit_Runner_Version::id();
if ('@package_version@' !== $phpUnitVersion && version_compare($phpUnitVersion, '3.5.0', '<')) {
    echo 'This version of PHPUnit (' . PHPUnit_Runner_Version::id() . ') is not supported in Zend Framework 2.x unit tests.' . PHP_EOL;
    exit(1);
}
unset($phpUnitVersion);

if (!file_exists(__DIR__ . '/../vendor/autoload.php') && !file_exists(__DIR__ . '/../../../autoload.php')) {
    echo "Composer dependencies not installed.";
    exit(1);
}

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$root    = realpath(dirname(__DIR__));
$library = "$root/library";
$tests   = "$root/tests";

/*
 * Prepend the Geometria Lab ZF Library library/ and tests/ directories to the
 * include_path. This allows the tests to run out of the box and helps prevent
 * loading other copies of the framework code and tests that would supersede
 * this copy.
 */
$path = array(
    $library,
    $tests,
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $path));

/**
 * Setup autoloading
 */
include __DIR__ .  '/autoload.php';
include __DIR__ . '/../../../autoload.php';

/*
 * Load the user-defined test configuration file, if it exists; otherwise, load
 * the default configuration.
 */
if (is_readable($tests . DIRECTORY_SEPARATOR . 'configuration.php')) {
    require_once $tests . DIRECTORY_SEPARATOR . 'configuration.php';
} else {
    require_once $tests . DIRECTORY_SEPARATOR . 'configuration.php.dist';
}

if (defined('TESTS_GENERATE_REPORT') && TESTS_GENERATE_REPORT === true) {
    $codeCoverageFilter = PHP_CodeCoverage_Filter::getInstance();

    $lastArg = end($_SERVER['argv']);
    if (is_dir($tests . DIRECTORY_SEPARATOR . $lastArg)) {
        $codeCoverageFilter->addDirectoryToWhitelist($tests . DIRECTORY_SEPARATOR . $lastArg);
    } else if (is_file($tests . DIRECTORY_SEPARATOR . $lastArg)) {
        $codeCoverageFilter->addDirectoryToWhitelist(dirname($tests . DIRECTORY_SEPARATOR . $lastArg));
    } else {
        $codeCoverageFilter->addDirectoryToWhitelist($library);
    }

    /*
     * Omit from code coverage reports the contents of the tests directory
     */
    $codeCoverageFilter->addDirectoryToBlacklist($tests, '');
    $codeCoverageFilter->addDirectoryToBlacklist(PEAR_INSTALL_DIR, '');
    $codeCoverageFilter->addDirectoryToBlacklist(PHP_LIBDIR, '');

    unset($codeCoverageFilter);
}

/*
 * Unset global variables that are no longer needed.
 */
unset($root, $library, $tests, $path);