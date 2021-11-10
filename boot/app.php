<?php

use App\Application;

// constants
define("APP_BASE_PATH", realpath(__DIR__ . '/..'));

// core
date_default_timezone_set('UTC');

// logs
error_reporting(-1);
ini_set("log_errors", 1);
ini_set("error_log", APP_BASE_PATH . '/logs/php_error.log');

// autoloader
require APP_BASE_PATH . '/boot/autoloader.php';

// helpers
require APP_BASE_PATH . '/app/helpers.php';

// config
require APP_BASE_PATH . '/app/Application.php';
Application::i();
