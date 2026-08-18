<?php
declare(strict_types=1);

define('APP_NAME', 'RimonTech');
define('APP_TAGLINE', 'Web Solutions That Grow Your Business');
define('APP_EMAIL', 'raihanrimon853@gmail.com');
define('APP_PHONE', '+8801875895858');
define('APP_ADDRESS', 'Shyamoli, Ring Road, Dhaka-Housing/06, Dhaka, Bangladesh');

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'rimontech');
define('DB_USER', getenv('DB_USER') ?: 'rimontech');
define('DB_PASS', getenv('DB_PASS') ?: 'rimontech_dev_2026');

define('UPLOAD_DIR', dirname(__DIR__) . '/uploads');
define('DOWNLOAD_DIR', dirname(__DIR__) . '/downloads');
define('MAX_UPLOAD_MB', 10);

if (!defined('APP_TIMEZONE')) {
    date_default_timezone_set('Asia/Dhaka');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
