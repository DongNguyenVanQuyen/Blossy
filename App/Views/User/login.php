
<?php include_once __DIR__ . '/../../Includes/config.php'; ?>

<!-- Head -->
<?php include_once __DIR__ . '/../../Includes/head.php'; ?>

<!-- Header -->
<?php include_once __DIR__ . '/../Layouts/Header.php'; ?>

<div class="auth-container">
  <div class="auth-box">
    <h2>Đăng nhập</h2>
    <form method="post" action="<?= BASE_URL ?>index.php?controller=auth&action=handleLogin">

      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Mật khẩu:</label>
      <input type="password" name="password" required>

      <button type="submit">Đăng nhập</button>

      <p class="auth-switch">
        Chưa có tài khoản? <a href="<?= BASE_URL ?>index.php?controller=auth&action=register">Đăng ký ngay</a>
      </p>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
