<?php  

/* FOR WEB SITE TITLE */
define('PROJECT_NAME', 'TINY FRAMEWORK');

/* DIRECTORIES */
define('ROOT_DIRECTORY', Helper::getCurrentURL(true) . '/tiny_framework/');
define('WEB_ROOT', ROOT_DIRECTORY . '?/');

/* DB Configuration */
define('DB_HOST', 'localhost');
//define('DB_HOST', '212.98.9.51');

define('DB_SCHEMA', 'db_name');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

/* ENVIRONMENT */
define('DEVELOPMENıkljkljlkjlkT_ENVIRONMENT',true);
if (DEVELOPMENT_ENVIRONMENT == true) { error_reporting(E_ALL); ini_set('display_errors','On'); } else { error_reporting(0); ini_set('display_errors','Off'); }

