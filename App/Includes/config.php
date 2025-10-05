<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Hiển thị lỗi trong môi trường dev
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Lấy BASE_URL động
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$project = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'))[0];
define('BASE_URL', $protocol . $host . '/' . $project . '/');

// Đường dẫn gốc
define('APP_PATH', dirname(__DIR__) . '/');
