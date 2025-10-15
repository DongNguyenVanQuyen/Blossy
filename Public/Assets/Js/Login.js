// ===============================
// LOGIN VALIDATION + TOAST
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
  const loginForm = document.querySelector(".auth-box form");
  if (!loginForm) return;

  loginForm.addEventListener("submit", (e) => {
    const email = loginForm.querySelector('input[name="email"]').value.trim();
    const password = loginForm
      .querySelector('input[name="password"]')
      .value.trim();

    if (!email || !password) {
      e.preventDefault();
      showToast("Vui lòng nhập đầy đủ Email và Mật khẩu!", "warning");
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      e.preventDefault();
      showToast("Email không hợp lệ!", "error");
      return;
    }

    // OK
    showToast("Đang đăng nhập...", "success");
  });
});
