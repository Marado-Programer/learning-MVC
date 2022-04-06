<?php

/**
 * functions that can be used in most of the files
 */

// Verify if the array has an specific key
function checkArray($array, $key)
{
    if (isset($array[$key]) && !empty($array[$key]))
        return $array[$key];
    return null;
}

function classAutoloader($className)
{
    $file = ROOT_PATH . '/src/classes/' . $className . '.php';
    if (!file_exists($file)) {
        require_once NOT_FOUND;
        return null;
    }

    require_once $file;
}
spl_autoload_register('classAutoloader');

