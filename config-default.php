<?php

// Debug
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Application
define('BASE_URL', '');
define('PAGE_LIMIT', 10);
define('CACHE_ENABLED', false);

define('TRENDS_ENABLED', false); // alpha
define('TRENDS_SECONDS_OFFSET', 2592000);
define('TRENDS_MIN_LENGHT', 4);
define('TRENDS_LIMIT', 40);

// Database
define('DB_NAME', '../kvazar.sqlite');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
