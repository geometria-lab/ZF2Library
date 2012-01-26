<?php

ini_set('display_errors', 1);

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(__DIR__ . '/../../../../../library'),
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('GeometriaLab');

$path = __DIR__ . '/map.ini';

$config = new Zend_Config_Ini($path, 'production');

$map = new GeometriaLab_VBuckets_Map_Config($config);
$hash = new GeometriaLab_VBuckets_HashMethod_Modulo();

$vBuckets = new GeometriaLab_VBuckets($map, $hash);

$bucket = $vBuckets->getByKey(1231);
print_r($bucket);

$bucket = $vBuckets->getByKey(32131);
print_r($bucket);

$bucket = $vBuckets->getByKey(534);
print_r($bucket);

$bucket = $vBuckets->getByKey(1235);
print_r($bucket);

$bucket = $vBuckets->getByKey('adasda');
print_r($bucket);

$bucket = $vBuckets->getByKey('fdsfs');
print_r($bucket);

$bucket = $vBuckets->getByKey('fsdf2234');
print_r($bucket);