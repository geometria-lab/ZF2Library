<?php

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(__DIR__ . '/../../../../library'),
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('GeometriaLab_');

require_once 'models/User.php';

$user = new User;
print $user->firstName;
print $user->secondName;

$user = new User(array('firstName' => 1, 'last' => 5));
print $user->firstName;
print $user->secondName;