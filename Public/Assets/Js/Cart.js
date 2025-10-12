// =======================
// GIỎ HÀNG AJAX FULL MƯỢT
// =======================
document.addEventListener("DOMContentLoaded", () => {
  const cartList = document.querySelector(".cart-list");
  if (!cartList) return;

  // ====== Tăng / Giảm số lượng ======
  cartList.addEventListener("click", async (e) => {
    const btn = e.target.closest(".qty-btn");
    if (!btn) return;

    const item = btn.closest(".cart-item");
    const id = item.dataset.id;
    const qtySpan = item.querySelector(".cart-quantity span");
    let qty = parseInt(qtySpan.textContent) || 1;

    if (btn.classList.contains("plus")) qty++;
    if (btn.classList.contains("minus") && qty > 1) qty--;

    item.style.opacity = 0.5;

    try {
      const res = await fetch("index.php?controller=cart&action=update", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `product_id=${id}&quantity=${qty}`,
      });

      const data = await res.json();
      if (!data.success) {
        alert(data.message || "Không thể cập nhật số lượng!");
        return;
      }

      // ✅ Cập nhật lại DOM
      qtySpan.textContent = data.quantity; // số lượng mới

      const price = parseInt(
        item.querySelector(".cart-price").textContent.replace(/\D/g, "")
      );
      const newSubtotal = price * data.quantity;
      item.querySelector(".cart-subtotal").textContent =
        newSubtotal.toLocaleString("vi-VN") + "đ";

      // Tổng cộng & tạm tính
      document
        .querySelectorAll(".summary-item")[0]
        .querySelector("span").textContent = data.totalItems;
      document
        .querySelectorAll(".summary-item")[1]
        .querySelector("span").textContent = data.subtotal;
      document.querySelector(".summary-item.total span").textContent =
        data.subtotal;

      // Nếu vượt tồn kho → thông báo
      if (data.message.includes("tồn kho")) {
        showToast("⚠️ " + data.message, "warning");
      } else {
        showToast("✅ " + data.message, "success");
      }
    } catch (err) {
      console.error("AJAX update error:", err);
      alert("Lỗi khi cập nhật giỏ hàng!");
    } finally {
      item.style.opacity = 1;
    }
  });

  // ====== Xóa từng sản phẩm ======
  cartList.addEventListener("click", async (e) => {
    const removeBtn = e.target.closest(".cart-remove");
    if (!removeBtn) return;
    if (!confirm("Bạn có chắc muốn xóa sản phẩm này?")) return;

    const item = removeBtn.closest(".cart-item");
    const id = item.dataset.id;

    try {
      const res = await fetch("index.php?controller=cart&action=remove", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `product_id=${id}`,
      });

      const data = await res.json();
      if (data.success) {
        item.remove();
        showToast("🗑️ Đã xóa sản phẩm.", "success");

        // Cập nhật lại tổng
        document
          .querySelectorAll(".summary-item")[0]
          .querySelector("span").textContent = data.totalItems;
        document
          .querySelectorAll(".summary-item")[1]
          .querySelector("span").textContent = data.subtotal;
        document.querySelector(".summary-item.total span").textContent =
          data.subtotal;

        // Nếu giỏ trống
        if (!document.querySelector(".cart-item")) {
          document.querySelector(".cart-list").innerHTML =
            '<p class="empty-cart">🛒 Giỏ hàng của bạn đang trống.</p>';
        }
      } else {
        alert(data.message || "Không thể xóa sản phẩm!");
      }
    } catch (err) {
      console.error("Xóa giỏ hàng lỗi:", err);
      alert("Không thể xóa sản phẩm!");
    }
  });

  // ====== Xóa toàn bộ giỏ hàng ======
  const clearBtn = document.querySelector(".clear-cart");
  if (clearBtn) {
    clearBtn.addEventListener("click", async () => {
      if (!confirm("Bạn có chắc muốn xóa toàn bộ giỏ hàng không?")) return;
      const res = await fetch("index.php?controller=cart&action=clear", {
        method: "POST",
      });
      const data = await res.json();
      if (data.success) {
        showToast("🗑️ Giỏ hàng đã được xóa.", "success");
        document.querySelector(".cart-list").innerHTML =
          '<p class="empty-cart">🛒 Giỏ hàng của bạn đang trống.</p>';
        document
          .querySelectorAll(".summary-item span")
          .forEach((s) => (s.textContent = "0"));
      }
    });
  }
});

// ====== Hàm Toast nho nhỏ ======
function showToast(message, type = "success") {
  const toast = document.createElement("div");
  toast.className = `toast ${type}`;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => toast.classList.add("show"), 50);
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 300);
  }, 2500);
}
