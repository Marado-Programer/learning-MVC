<?php

/**
 * 
 */

if (!defined('ROOT_PATH'))
    exit;

session_start();

if (!defined('DEBUG') || DEBUG === false) {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', 1);
}

require_once ROOT_PATH . '/src/global/global-functions.php';

new System();

