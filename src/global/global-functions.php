<?php

/**
 * functions that can be used in most of the files
 */

function checkArray($array, $key)
{
    if (isset($array[$key]) && !empty($array[$key]))
        return $array[$key];
    return null;
}

function my_autoloader($class_name)
{
    $file = ROOTPATH . '/src/classes/' . $class_name . '.php';
    if (!file_exists($file)) {
        require_once ROOTPATH . '/404.php';
        return;
    }

    require_once $file;
}

spl_autoload_register('my_autoloader');

