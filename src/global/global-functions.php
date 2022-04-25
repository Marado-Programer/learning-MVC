<?php

/**
 * functions that can be used in most of the files
 */

// Verify if the array has an specific key
function checkArray(array $array, int|string ...$keys)
{
    if (!isset($array))
        return;

    $keys = $keys ?: array_keys($array);

    foreach ($keys as $key) {
        if (!isset($array[$key]) || empty($array[$key]))
            return;
        $arr[$key] = $array[$key];
    }
    
    if (count($arr) > 1)
        return $arr;

    return $arr[$keys[0]];
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

