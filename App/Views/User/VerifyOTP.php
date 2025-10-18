<?php include_once __DIR__ . '/../../Includes/config.php'; ?>
<?php include_once __DIR__ . '/../../Includes/head.php'; ?>
<?php include_once __DIR__ . '/../Layouts/Header.php'; ?>
<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/User/VerifyOTP.css?v=<?= time() ?>">
<div class="register-container">
  <div class="register-box">
    <h2>Xác thực OTP</h2>
    <form action="index.php?controller=auth&action=handleVerifyOTP" method="post">
      <div class="field">
        <label>Nhập mã OTP (6 số):</label>
        <input type="text" name="otp" maxlength="6" required>
      </div>
      <button type="submit">Xác nhận</button>
    </form>
  </div>
</div>
<?php include_once __DIR__ . '/../../Includes/Script.php' ?>