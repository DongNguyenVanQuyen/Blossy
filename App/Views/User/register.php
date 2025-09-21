<?php include_once __DIR__ . '/../../Includes/config.php'; ?>
<?php include_once __DIR__ . '/../../Includes/head.php'; ?>
<?php include_once '../Layouts/Header.php'; ?>
<div class="register-container">
  <div class="register-box">
    <h2>Đăng Ký</h2>
    <form action="register_process.php" method="post">
      <div class="register-grid">
        <!-- BÊN TRÁI -->
        <div class="register-left">
          <div class="row-inline">
            <div class="field half">
              <label>Họ:</label>
              <input type="text" name="first_name" required>
            </div>
            <div class="field half">
              <label>Tên:</label>
              <input type="text" name="last_name" required>
            </div>
          </div>

          <div class="field">
            <label>Email:</label>
            <input type="email" name="email" required>
          </div>

          <div class="field">
            <label>Số điện thoại:</label>
            <input type="text" name="phone" required>
          </div>
        </div>

        <!-- BÊN PHẢI -->
        <div class="register-right">
          <div class="field">
            <label>Mật khẩu:</label>
            <input type="password" name="password" required>
          </div>

          <div class="field">
            <label>Xác nhận mật khẩu:</label>
            <input type="password" name="confirm_password" required>
          </div>

          <div class="field">
            <label>Địa chỉ:</label>
            <input type="text" name="address" required>
          </div>
        </div>
      </div>

      <button type="submit">Đăng ký</button>
      <p class="auth-switch">
        Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
      </p>
    </form>
  </div>
</div>


<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
