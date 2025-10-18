<?php include_once __DIR__ . '/../../Includes/config.php'; ?>
<?php include_once __DIR__ . '/../../Includes/head.php'; ?>
<?php include_once __DIR__ . '/../Layouts/Header.php'; ?>
<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/User/Login_Register.css?v=<?= time() ?>">
<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/User/ForgotPassword_OTP.css?v=<?= time() ?>">


<div class="register-container">
  <div class="register-box">
    <h2>Quên mật khẩu</h2>

    <!-- 🔹 CHỈ 1 FORM DUY NHẤT -->
    <form id="forgotForm" action="index.php?controller=auth&action=handleForgotPassword" method="post">
      <!-- Email -->
      <div class="field">
        <label>Nhập email của bạn:</label>
        <input type="email" name="email" id="email" placeholder="example@gmail.com" required>
      </div>

      <button type="button" id="btnSendOTP" class="otp-btn">Gửi mã OTP</button>

      <hr style="margin: 25px 0; border: 0.5px solid var(--header-border);" />

      <!-- Nhập OTP -->
      <div class="field">
        <label>Mã OTP (6 số):</label>
        <input type="text" name="otp" maxlength="6" placeholder="Nhập mã OTP bạn nhận được">
      </div>

      <!-- Mật khẩu mới -->
      <div class="field">
        <label>Mật khẩu mới:</label>
        <input type="password" name="password" placeholder="Nhập mật khẩu mới">
      </div>

      <div class="field">
        <label>Xác nhận mật khẩu:</label>
        <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới">
      </div>

      <button type="submit" name="action" value="reset_password" class="reset-btn">Đặt lại mật khẩu</button>

      <p class="auth-switch">
        <a href="<?= BASE_URL ?>index.php?controller=auth&action=login">← Quay lại đăng nhập</a>
      </p>
    </form>
  </div>
</div>

<!-- Toast -->
<script src="<?= BASE_URL ?>Public/Assets/Js/Toast.js?v=<?= time() ?>"></script>
<?php if (!empty($_SESSION['toast'])): ?>
<script>showToast("<?= addslashes($_SESSION['toast']['message']) ?>","<?= $_SESSION['toast']['type'] ?>");</script>
<?php unset($_SESSION['toast']); endif; ?>

<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>

<script>
// 🔹 Gửi OTP bằng JS (chỉ 1 form)
document.getElementById("btnSendOTP").addEventListener("click", function() {
  const form = document.getElementById("forgotForm");
  const email = document.getElementById("email").value.trim();

  if (!email) {
    showToast("Vui lòng Nhập Email trước!","warning")
    return;
  }

  // Thêm input ẩn để phân biệt hành động
  const hidden = document.createElement("input");
  hidden.type = "hidden";
  hidden.name = "action";
  hidden.value = "send_otp";
  form.appendChild(hidden);

  form.submit();

  // Vô hiệu hóa nút trong 60s
  this.disabled = true;
  let countdown = 60;
  const timer = setInterval(() => {
    this.textContent = `Gửi lại sau ${countdown--}s`;
    if (countdown < 0) {
      clearInterval(timer);
      this.disabled = false;
      this.textContent = "Gửi mã OTP";
    }
  }, 1000);
});
</script>
