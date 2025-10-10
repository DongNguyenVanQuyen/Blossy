// ====================
// XỬ LÝ THANH TOÁN
// ====================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("checkout-form");
  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const btn = form.querySelector(".checkout-btn");
    btn.disabled = true;
    btn.textContent = "Đang xử lý...";

    try {
      const res = await fetch(form.getAttribute("action"), {
        method: "POST",
        body: formData,
      });
      const data = await res.json();

      if (data.success) {
        showToast("🎉 Thanh toán thành công!", "success");
        setTimeout(() => (window.location.href = data.redirect), 800);
      } else {
        showToast(data.message || "Không thể hoàn tất thanh toán.", "error");
        btn.disabled = false;
        btn.textContent = "Thanh Toán";
      }
    } catch (err) {
      console.error("❌ Lỗi thanh toán:", err);
      showToast("Có lỗi xảy ra, vui lòng thử lại.", "error");
      btn.disabled = false;
      btn.textContent = "Thanh Toán";
    }
  });
});

// ====================
// ÁP DỤNG MÃ VOUCHER (AJAX)
// ====================
document.addEventListener("DOMContentLoaded", () => {
  const btnApply = document.getElementById("apply-voucher");
  const input = document.getElementById("voucher-input");
  const msg = document.getElementById("voucher-message");

  const subtotalText = document.querySelector(".summary-subtotal span");
  const discountText = document.querySelector(".summary-discount span");
  const totalText = document.querySelector(".summary-total span");

  if (!btnApply || !input) return;

  btnApply.addEventListener("click", async () => {
    const code = input.value.trim();
    if (!code) {
      showToast("⚠️ Vui lòng nhập mã voucher.", "warning");
      msg.textContent = "⚠️ Vui lòng nhập mã voucher.";
      msg.className = "voucher-error";
      return;
    }

    const subtotal = parseFloat(subtotalText.textContent.replace(/[^\d]/g, ""));

    try {
      const res = await fetch("index.php?controller=voucher&action=apply", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `code=${encodeURIComponent(code)}&subtotal=${subtotal}`,
      });

      const data = await res.json();

      if (data.success) {
        showToast("✅ Mã hợp lệ! Đã áp dụng giảm giá.", "success");
        msg.textContent = "✅ " + data.message + " - Giảm " + data.discount;
        msg.className = "voucher-success";

        discountText.textContent = "-" + data.discount;
        totalText.textContent = data.total;

        document.getElementById("voucher_code").value = data.code;
        document.getElementById("voucher_discount").value =
          data.discount.replace(/[^\d]/g, "");
      } else {
        showToast("❌ " + (data.message || "Mã không hợp lệ."), "error");
        msg.textContent = "❌ " + data.message;
        msg.className = "voucher-error";

        discountText.textContent = "-0đ";
        totalText.textContent = subtotal.toLocaleString("vi-VN") + "đ";
      }
    } catch (err) {
      console.error("Lỗi áp dụng voucher:", err);
      showToast("⚠️ Có lỗi khi áp dụng mã, thử lại.", "warning");
      msg.textContent = "⚠️ Có lỗi khi áp dụng mã, thử lại.";
      msg.className = "voucher-error";
    }
  });
});

// ====================
// HÀM HIỂN THỊ TOAST
// ====================
function showToast(message, type = "success") {
  let toast = document.createElement("div");
  toast.className = `toast ${type}`;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => toast.classList.add("show"), 50);
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 300);
  }, 2500);
}
