<?php

/**
 *  Default Configuration for the project Enviorment
 */

define('ROOTPATH', dirname(__FILE__) . '/..');
define('UPLOADPATH', ROOTPATH . '/resources/uploads');
define('CONTROLLERSPATH', ROOTPATH . '/src/controllers');
define('HOME_URI', 'http://localhost/project');
define('DB_HOSTNAME', 'localhost');
define('DB_NAME', 'epccmvc');
define('DB_USERNAME', 'phpu');
define('DB_USER_PASSWORD', 'passwd1asdf');
define('DB_CHARSET', 'utf8');
define('DEBUG',  true);

require_once ROOTPATH . '/src/loader.php';

