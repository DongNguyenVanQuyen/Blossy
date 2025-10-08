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
        // ✅ Chuyển sang trang hoàn tất đơn hàng
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
