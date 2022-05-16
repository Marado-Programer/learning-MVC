<?php

/**
 * 
 */

if (!defined('FROM_INDEX') || !FROM_INDEX)
    exit();

require_once './config/config.php';
require_once './config/DBconfig.php';
require_once './config/URIconfig.php';

defined('ROOT_PATH') AND defined('DB_NAME') AND defined('DEBUG') OR exit();

session_start();

if (!defined('DEBUG') || DEBUG === false) {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', 1);
}

require_once ROOT_PATH . '/src/global/global-functions.php';

$sys = new System();
$sys->genController();
