<?php

function autoload($class) {
    $class = ltrim($class,'\\');
    $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\',DIRECTORY_SEPARATOR,$class) . '.php';
    if(is_file($file)) {
        require $file;
        return true;
    }
    return false;
}

spl_autoload_register('autoload');