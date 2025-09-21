<?php
session_start();
include_once __DIR__ . '/../../Includes/config.php';
include_once __DIR__ . '/../../Models/Database/database.php';
include_once __DIR__ . '/../../Models/Database/DBHandler.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $db = new DBHandler();
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $user = $db->readitem($sql);

    if (!empty($user)) {
        $_SESSION['user'] = $user[0]; // lưu thông tin user
        echo "<script>alert('✅ Đăng nhập thành công!'); window.location.href='../../../Public/index.php';</script>";
    } else {
        echo "<script>alert('❌ Sai tài khoản hoặc mật khẩu!'); window.history.back();</script>";
    }
}
