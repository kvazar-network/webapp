<?php

// Debug
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Application
define('BASE_URL', 'https://kvazar.today/');
define('PAGE_LIMIT', 10);
define('CACHE_ENABLED', false);

// Database
define('DB_NAME', '../kvazar.sqlite');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
