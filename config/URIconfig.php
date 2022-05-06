<?php

/**
 * URI for the project
 */

// path for the root of this project
define('ROOT_PATH', dirname(dirname(__FILE__)));

define('UPLOAD_PATH', ROOT_PATH . '/public/uploads');
define('VIEWS_PATH', ROOT_PATH . '/public/views');
define('CONTROLLERS_PATH', ROOT_PATH . '/src/controllers');

define('HOME_URI', 'http://' . $_SERVER['SERVER_NAME'] . '/learning-MVC');

define('STYLE_URI', HOME_URI . '/public/styles');
define('SCRIPT_URI', HOME_URI . '/public/scripts');

define('NOT_FOUND', ROOT_PATH . '/public/404.php');
