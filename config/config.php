<?php

/**
 *  Default Configuration for the project Enviorment
 */

define('ROOT_PATH', dirname(__FILE__) . '/..');
define('UPLOAD_PATH', ROOT_PATH . '/resources/uploads');
define('VIEWS_PATH', ROOT_PATH . '/public/views');
define('CONTROLLERS_PATH', ROOT_PATH . '/src/controllers');
define('HOME_URI', 'http://localhost/project');
define('DB_HOSTNAME', 'localhost');
define('DB_NAME', 'epccmvc');
define('DB_USERNAME', 'root');
define('DB_USER_PASSWORD', '');
define('DB_CHARSET', 'utf8');
define('DEBUG', true);
define('NOT_FOUND', ROOT_PATH . '/public/404.php');

require_once ROOT_PATH . '/src/loader.php'; // improve -> PSR-1 - 2.3.

