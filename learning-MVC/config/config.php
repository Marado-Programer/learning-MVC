<?php

/**
 *  Default Configuration for the project enviorment
 */

// path for the root of this project
define('ROOT_PATH', dirname(dirname(__FILE__)));

define('UPLOAD_PATH', ROOT_PATH . '/public/uploads');
define('VIEWS_PATH', ROOT_PATH . '/public/views');
define('CONTROLLERS_PATH', ROOT_PATH . '/src/controllers');

define('HOME_URI', 'http://' . $_SERVER['SERVER_NAME'] . '/project');

define('STYLE_URI', HOME_URI . '/public/styles');
define('SCRIPT_URI', HOME_URI . '/public/scripts');

// Config for PDO
define('DB_HOSTNAME', 'localhost');
define('DB_NAME', 'mvcProject');
define('DB_USERNAME', 'mvcuser');
define('DB_USER_PASSWORD', 'mvc1user!passwd');
define('DB_CHARSET', 'utf8');

// true when programming
define('DEBUG', true);

define('NOT_FOUND', ROOT_PATH . '/public/404.php');

