<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start session
session_start();

// set expare time of user's session
$_SESSION['expire_time'] = 60 * 30;
date_default_timezone_set('Europe/Helsinki');

require_once('database.php');

// Application URL
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $urlroot = "https://";
else
    $urlroot = "http://";
// Append the host(domain name, ip) to the URL.
$urlroot .= $_SERVER['HTTP_HOST'];
// Append the requested resource location to the URL
$urlroot .= dirname($_SERVER['PHP_SELF']);

// Define application URL
define('URLROOT', $urlroot);
// Define application root folder
define('APPNAME', basename(dirname($_SERVER['PHP_SELF'])));
// Define the whole path to application root folder
define('APPROOT', dirname(dirname(__FILE__)));

// Autoload Components
spl_autoload_register(function ($class) {
    require_once APPROOT . '/components/' . $class . '.php';
});

if ($ex = Db::connect($DB_DSN, $DB_USER, $DB_PASSWORD, $DB_NAME)) {
    exit('You have database error: ' . $ex->getMessage());
}
