<?php
// Bắt đầu session (bắt buộc nếu có login, giỏ hàng)
session_start();

// Thiết lập múi giờ mặc định
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Tự động lấy URL gốc của project
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$project = "/Web_Hoa"; // tên folder gốc của bạn

define("BASE_URL", $protocol . $host . $project . "/");

// Sau này bạn sẽ thêm kết nối DB tại đây
// define('BASE_URL', 'http://localhost/WebBanHoa/');
?>


