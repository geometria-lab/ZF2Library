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

    // $segments = explode('\\', $class); // preg_split('#\\\\|_#', $class);//
    $segments = preg_split('#[\\\\_]#', $class); // preg_split('#\\\\|_#', $class);//
    $ns       = array_shift($segments);

    switch ($ns) {
        case 'GeometriaLab':
            $file = dirname(__DIR__) . '/library/GeometriaLab/';
            break;
        case 'GeometriaLabTest':
            // temporary fix for ZendTest namespace until we can migrate files 
            // into ZendTest dir
            $file = __DIR__ . '/GeometriaLab/';
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

    $segments = explode('_', $class);
    $ns       = array_shift($segments);

    switch ($ns) {
        case 'GeometriaLab':
            $file = dirname(__DIR__) . '/library/GeometriaLab/';
            break;
        default:
            return false;
    }
    $file .= implode('/', $segments) . '.php';
    if (file_exists($file)) {
        return include_once $file;
    }

    return false;
}
spl_autoload_register('GeometriaLabTest_Autoloader', true, true);