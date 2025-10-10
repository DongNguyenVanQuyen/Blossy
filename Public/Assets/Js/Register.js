// ===============================
// REGISTER VALIDATION + TOAST
// ===============================

// Bảo đảm showToast có sẵn
if (typeof showToast === "undefined") {
  window.showToast = function (message, type = "success") {
    const t = document.createElement("div");
    t.className = `toast ${type}`;
    t.textContent = message;
    document.body.appendChild(t);
    setTimeout(() => t.classList.add("show"), 50);
    setTimeout(() => {
      t.classList.remove("show");
      setTimeout(() => t.remove(), 300);
    }, 2500);
  };
}

document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.querySelector(".register-box form");
  if (!registerForm) return;

  registerForm.addEventListener("submit", (e) => {
    const firstName = registerForm
      .querySelector('input[name="first_name"]')
      .value.trim();
    const lastName = registerForm
      .querySelector('input[name="last_name"]')
      .value.trim();
    const email = registerForm
      .querySelector('input[name="email"]')
      .value.trim();
    const phone = registerForm
      .querySelector('input[name="phone"]')
      .value.trim();
    const password = registerForm
      .querySelector('input[name="password"]')
      .value.trim();
    const confirm = registerForm
      .querySelector('input[name="confirm_password"]')
      .value.trim();
    const address = registerForm
      .querySelector('input[name="address"]')
      .value.trim();

    if (
      !firstName ||
      !lastName ||
      !email ||
      !phone ||
      !password ||
      !confirm ||
      !address
    ) {
      e.preventDefault();
      showToast("⚠️ Vui lòng nhập đầy đủ thông tin!", "warning");
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      e.preventDefault();
      showToast("❌ Email không hợp lệ!", "error");
      return;
    }

    const phoneRegex = /^[0-9]{9,11}$/;
    if (!phoneRegex.test(phone)) {
      e.preventDefault();
      showToast("❌ Số điện thoại phải có 9-11 chữ số!", "error");
      return;
    }

    if (password.length < 6) {
      e.preventDefault();
      showToast("⚠️ Mật khẩu phải có ít nhất 6 ký tự!", "warning");
      return;
    }

    if (password !== confirm) {
      e.preventDefault();
      showToast("❌ Mật khẩu xác nhận không khớp!", "error");
      return;
    }

    // OK
    showToast("✅ Đang xử lý đăng ký...", "success");
  });
});
