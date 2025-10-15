// ====== XÓA SẢN PHẨM KHỎI YÊU THÍCH ======
document.querySelectorAll(".wishlist__remove").forEach((btn) => {
  btn.addEventListener("click", function () {
    const id = this.dataset.productId;

    fetch(`index.php?controller=favorites&action=toggle`, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `product_id=${id}`,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success && !data.favorited) {
          // Xóa hàng khỏi danh sách
          this.closest(".wishlist__row").remove();
          showToast(
            data.message || "🗑️ Đã xóa khỏi danh sách yêu thích!",
            "warning"
          );

          // Cập nhật lại số lượng badge yêu thích
          updateHeaderCounts();
        } else {
          showToast(data.message || "Không thể xóa khỏi yêu thích!", "error");
        }
      })
      .catch((err) => {
        console.error("Lỗi xóa yêu thích:", err);
        showToast("Lỗi mạng, thử lại sau!", "error");
      });
  });
});

// ====== THÊM SẢN PHẨM VÀO GIỎ ======
document.querySelectorAll(".wishlist__add-btn").forEach((btn) => {
  btn.addEventListener("click", async function () {
    if (btn.classList.contains("disabled")) return; // hết hàng thì bỏ qua

    const row = btn.closest(".wishlist__row");
    const productId = row.querySelector(".wishlist__remove").dataset.productId;

    btn.disabled = true;
    btn.textContent = "Đang thêm...";

    try {
      const res = await fetch("index.php?controller=cart&action=add", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          product_id: productId,
          quantity: 1,
        }),
      });

      const data = await res.json();

      if (data.success) {
        showToast("🛒 Đã thêm vào giỏ hàng!", "success");

        // ✅ Cập nhật lại số lượng giỏ hàng
        updateHeaderCounts();
      } else {
        showToast(data.message || "Không thể thêm vào giỏ hàng!", "error");
      }
    } catch (err) {
      console.error("Lỗi thêm vào giỏ:", err);
      showToast("Lỗi mạng, thử lại sau!", "error");
    } finally {
      btn.disabled = false;
      btn.textContent = "Thêm Vào Giỏ";
    }
  });
});

// ====== Toast thông báo ======
function showToast(message, type = "success") {
  // Xóa toast cũ (tránh bị chồng)
  const oldToast = document.querySelector(".toast");
  if (oldToast) oldToast.remove();

  const toast = document.createElement("div");
  toast.className = `toast ${type}`;

  // Icon tự động theo loại
  const icon =
    type === "success"
      ? "✅"
      : type === "error"
      ? "❌"
      : type === "warning"
      ? "⚠️"
      : "ℹ️";

  toast.innerHTML = `
    <span class="toast__icon">${icon}</span>
    <span class="toast__msg">${message}</span>
  `;

  document.body.appendChild(toast);

  setTimeout(() => toast.classList.add("show"), 50);
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 300);
  }, 2500);
}
