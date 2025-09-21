

<?php
include_once __DIR__ . '/../../Includes/config.php';
include_once __DIR__ . '/../../Models/Database/database.php';
include_once __DIR__ . '/../../Models/Database/DBHandler.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $password   = trim($_POST['password']);
    $confirm    = trim($_POST['confirm_password']);
    $phone      = trim($_POST['phone']);
    $address    = trim($_POST['address']);

    if ($password !== $confirm) {
        echo "<script>alert('❌ Mật khẩu không khớp!'); window.history.back();</script>";
        exit;
    }

    $db = new DBHandler();

    // Kiểm tra trùng email
    $check = $db->readitem("SELECT * FROM users WHERE email = '$email'");
    if (!empty($check)) {
        echo "<script>alert('❌ Email đã tồn tại!'); window.history.back();</script>";
        exit;
    }

    // Thêm người dùng
    $sql = "INSERT INTO users (email, password, first_name, last_name, phone, address)
            VALUES ('$email', '$password', '$first_name', '$last_name', '$phone', '$address')";
    $db->execute($sql);
    
        // Hash mật khẩu
    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    // $sql = "INSERT INTO users (email, password, full_name, phone, address)
    //                 VALUES ('$email', '$hashedPassword', '$full_name', '$phone', '$address')";
    //  $db->execute($sql);


    echo "<script>alert('✅ Đăng ký thành công!'); window.location.href='login.php';</script>";

}
?>



