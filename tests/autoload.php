<?php

/**
 * Setup autoloading
 */
function GeometriaLabTest_Autoloader($class)
{
    $class = ltrim($class, '\\');

    if (!preg_match('#^(GeometriaLab(Test)?|PHPUnit)(\\\\|_)#', $class)) {
        return false;
    }

    $segments = explode('\\', $class);
    $ns       = array_shift($segments);

    switch ($ns) {
        case 'GeometriaLab':
            $file = dirname(__DIR__) . '/library/GeometriaLab/';
            break;
        case 'GeometriaLabTest':
            $file = __DIR__ . '/GeometriaLabTest/';
            break;
        default:
            $file = false;
            break;
    }

    if ($file) {
        $file .= implode('/', $segments) . '.php';
        if (file_exists($file)) {
            return include_once $file;
        }
    }

    return false;
}
spl_autoload_register('GeometriaLabTest_Autoloader', true, true);