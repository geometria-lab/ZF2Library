<?php

define('DATA_DIR', realpath(__DIR__ . '/data'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(__DIR__ . '/../vendor'),
    realpath(__DIR__ . '/../library'),
    realpath(__DIR__ . '/library'),
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace('GeometriaLab');

//GeometriaRu_Test_HelperBroker::addPath(LIBRARY_PATH . '/GeometriaRu/Test/Helper', 'GeometriaRu_Test_Helper');

//require_once 'GeometriaRu/Test/PHPUnit/Listener/Plugin.php';

//if (version_compare(GeometriaRu_Test_PHPUnit_Runner_Version::id(), '3.6.10', '<')) {
//    throw new GeometriaRu_Test_Exception('Version of PHPUnit less then 3.6.10');
//}