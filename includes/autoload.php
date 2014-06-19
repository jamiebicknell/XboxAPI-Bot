<?php

function autoload($class)
{
    $class = ltrim($class, '\\');
    $file = dirname(__FILE__) . '/' . str_replace(array('\\', '_'), '/', $class) . '.php';
    if (is_file($file)) {
        require $file;
        return true;
    }
    return false;
}

spl_autoload_register('autoload');
