<?php
require_once('config/config.php');

$router = new Router();
$router->render(trim($_SERVER['REQUEST_URI'], '/'));
