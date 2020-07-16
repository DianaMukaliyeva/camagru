<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start session
session_start();

require_once('database.php');

// Autoload Components
spl_autoload_register(function ($class) {
    require_once 'components/' . $class . '.php';
});

// Application URL
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $urlroot = "https://";
else
    $urlroot = "http://";
// Append the host(domain name, ip) to the URL.
$urlroot .= $_SERVER['HTTP_HOST'];
// Append the requested resource location to the URL
$urlroot .= dirname($_SERVER['PHP_SELF']);

define('URLROOT', $urlroot);
define('APPROOT', basename(dirname($_SERVER['PHP_SELF'])));

Db::connect($DB_DSN, $DB_USER, $DB_PASSWORD, $DB_NAME);
