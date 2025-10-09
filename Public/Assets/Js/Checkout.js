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
      // ✅ Dùng getAttribute để tránh trùng với input name="action"
      const res = await fetch(form.getAttribute("action"), {
        method: "POST",
        body: formData,
      });
      const data = await res.json();

      if (data.success) {
        window.location.href = data.redirect;
      } else {
        alert(data.message || "Không thể hoàn tất thanh toán.");
        btn.disabled = false;
        btn.textContent = "Thanh Toán";
      }
    } catch (err) {
      console.error("❌ Lỗi thanh toán:", err);
      alert("Có lỗi xảy ra, vui lòng thử lại.");
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
        msg.textContent = "✅ " + data.message + " - Giảm " + data.discount;
        msg.className = "voucher-success";

        discountText.textContent = "-" + data.discount;
        totalText.textContent = data.total;

        // ✅ Ghi giá trị thật vào form để gửi qua PHP
        document.getElementById("voucher_code").value = data.code;
        document.getElementById("voucher_discount").value =
          data.discount.replace(/[^\d]/g, "");
      } else {
        msg.textContent = "❌ " + data.message;
        msg.className = "voucher-error";

        discountText.textContent = "-0đ";
        totalText.textContent = subtotal.toLocaleString("vi-VN") + "đ";
      }
    } catch (err) {
      console.error("Lỗi áp dụng voucher:", err);
      msg.textContent = "⚠️ Có lỗi khi áp dụng mã, thử lại.";
      msg.className = "voucher-error";
    }
  });
});
