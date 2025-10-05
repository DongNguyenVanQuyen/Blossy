<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');


// Lấy giao thức
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

// Lấy host (VD: localhost hoặc blossy.com)
$host = $_SERVER['HTTP_HOST'];

// Lấy tên folder gốc của project (VD: /Web_Hoa)
$uri_parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$project_name = $uri_parts[0]; // Lấy "Web_Hoa"

// Gộp lại thành BASE_URL
define("BASE_URL", $protocol . $host . '/' . $project_name . '/');
define("APP_PATH", dirname(__DIR__, 1) . "/"); 

?>