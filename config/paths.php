<?php

/**
 * this file defines some variables
 * those variables contain the website path and the source file path
 */

// get base directory dynamically for instance "C:/Users/sebl0/PhpstormProjects/TPS/"
define('BASE_DIRECTORY', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// get database directory dynamically for instance "C:/Users/sebl0/PhpstormProjects/TPS/src/Models/database/"
define('DATABASE_DIRECTORY', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR);

// $protocol = http or https
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

// $_SERVER['SCRIPT_NAME'] = http://localhost/TPS/public/index.php
$scriptPath = dirname($_SERVER['SCRIPT_NAME']); // filters for the directory so: $scriptPath = /TPS/public
if ($scriptPath == '\\'){
    $scriptPath = '';
}
define('PUBLIC_DIRECTORY', $protocol . $_SERVER['HTTP_HOST'] . $scriptPath . '/');