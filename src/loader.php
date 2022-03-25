<?php

/**
 * 
 */

if (!defined('ROOTPATH'))
    exit;

session_start();

if (!defined('DEBUG') || DEBUG === false) {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require_once ROOTPATH . '/src/global/global-functions.php';

$sys = new System();

