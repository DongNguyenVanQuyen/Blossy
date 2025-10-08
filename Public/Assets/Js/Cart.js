document.addEventListener("DOMContentLoaded", () => {
  const cartList = document.querySelector(".cart-list");
  if (!cartList) return;

  cartList.addEventListener("click", (e) => {
    const btn = e.target.closest(".qty-btn");
    if (!btn) return;

    const item = btn.closest(".cart-item");
    const productId = item.dataset.id;
    const quantityEl = item.querySelector(".cart-quantity span");
    let currentQty = parseInt(quantityEl.textContent);

    // ====== Tăng / giảm ======
    if (btn.classList.contains("plus")) currentQty++;
    if (btn.classList.contains("minus") && currentQty > 1) currentQty--;

    // Loading nhẹ
    item.style.opacity = 0.6;

    // ====== Gửi AJAX ======
    fetch("index.php?controller=cart&action=update", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        product_id: productId,
        quantity: currentQty,
      }),
    })
      .then((res) => res.text()) // đọc text thô
      .then((text) => {
        console.log("RAW RESPONSE:", text); // 👈 in ra console để debug
        let data;
        try {
          data = JSON.parse(text); // chuyển text sang JSON
        } catch (err) {
          console.error("JSON parse error:", err);
          alert("Phản hồi không phải JSON hợp lệ!");
          return;
        }

        if (!data.success) {
          alert(data.message || "Lỗi cập nhật giỏ hàng!");
          return;
        }

        // ====== Cập nhật giao diện ======
        quantityEl.textContent = currentQty;

        // Subtotal từng sản phẩm
        const priceText = item.querySelector(".cart-price").textContent;
        const price = parseInt(priceText.replace(/\D/g, ""));
        const newSubtotal = price * currentQty;
        item.querySelector(".cart-subtotal").textContent =
          newSubtotal.toLocaleString("vi-VN") + "đ";

        // Tổng sản phẩm + tạm tính + tổng cộng
        document.querySelector(".summary-item span").textContent =
          data.totalItems;
        document
          .querySelectorAll(".summary-item")[1]
          .querySelector("span").textContent = data.subtotal;
        document.querySelector(".summary-item.total span").textContent =
          data.subtotal;
      })
      .catch((err) => {
        console.error("AJAX Error:", err);
        alert("Không thể cập nhật số lượng!");
      })
      .finally(() => {
        item.style.opacity = 1;
      });
  });
});

// Xóa sản phẩm khỏi giỏ hàng
// =======================
// XÓA SẢN PHẨM KHỎI GIỎ HÀNG
// =======================
document.addEventListener("DOMContentLoaded", () => {
  const cartList = document.querySelector(".cart-list");
  if (!cartList) return;

  cartList.addEventListener("click", (e) => {
    const removeBtn = e.target.closest(".cart-remove");
    if (!removeBtn) return;

    if (!confirm("Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?")) return;

    const item = removeBtn.closest(".cart-item");
    const productId = item.dataset.id;

    // Gửi AJAX xóa
    fetch("index.php?controller=cart&action=remove", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ product_id: productId }),
    })
      .then((res) => res.text())
      .then((text) => {
        console.log("RAW REMOVE RESPONSE:", text);
        let data;
        try {
          data = JSON.parse(text);
        } catch {
          alert("Phản hồi không hợp lệ!");
          return;
        }

        if (data.success) {
          // Xóa phần tử khỏi DOM
          item.remove();

          // Cập nhật tổng giỏ hàng
          document.querySelector(".summary-item span").textContent =
            data.totalItems;
          document
            .querySelectorAll(".summary-item")[1]
            .querySelector("span").textContent = data.subtotal;
          document.querySelector(".summary-item.total span").textContent =
            data.subtotal;

          // Nếu hết sản phẩm → hiển thị thông báo trống
          const remainingItems = document.querySelectorAll(".cart-item");
          if (remainingItems.length === 0) {
            document.querySelector(".cart-list").innerHTML =
              '<p class="empty-cart">🛒 Giỏ hàng của bạn đang trống.</p>';
          }
        } else {
          alert(data.message || "Không thể xóa sản phẩm!");
        }
      })
      .catch(() => alert("Lỗi khi xóa sản phẩm!"));
  });
});
document.addEventListener("DOMContentLoaded", () => {
  // ===== Nút Thanh Toán =====
  const checkoutBtn = document.querySelector(".checkout-now-card");

  if (checkoutBtn) {
    checkoutBtn.addEventListener("click", async () => {
      try {
        // ✅ Gọi API kiểm tra giỏ hàng trước khi chuyển sang thanh toán
        const res = await fetch("index.php?controller=cart&action=index", {
          method: "GET",
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });

        if (!res.ok) {
          alert("Không thể tải giỏ hàng. Vui lòng thử lại.");
          return;
        }

        const htmlText = await res.text();
        if (htmlText.includes("empty-cart")) {
          alert("🛒 Giỏ hàng của bạn đang trống.");
          return;
        }

        // ✅ Nếu giỏ hàng có sản phẩm → chuyển sang trang Checkout
        window.location.href = "index.php?controller=checkout&action=index";
      } catch (err) {
        console.error("❌ Lỗi khi kiểm tra giỏ hàng:", err);
        alert("Đã xảy ra lỗi, vui lòng thử lại sau.");
      }
    });
  }

  // ===== Nút Xóa Toàn Bộ Giỏ Hàng =====
  const clearBtn = document.querySelector(".clear-cart");
  if (clearBtn) {
    clearBtn.addEventListener("click", async () => {
      if (!confirm("Bạn có chắc muốn xóa toàn bộ giỏ hàng không?")) return;

      try {
        const res = await fetch("index.php?controller=cart&action=clear", {
          method: "POST",
        });
        const data = await res.json();

        if (data.success) {
          alert("🗑️ Giỏ hàng đã được xóa.");
          location.reload();
        } else {
          alert("Không thể xóa giỏ hàng.");
        }
      } catch (err) {
        console.error("❌ Lỗi khi xóa giỏ hàng:", err);
      }
    });
  }

  // ===== Nút Xóa Từng Sản Phẩm =====
  document.querySelectorAll(".cart-remove").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const item = btn.closest(".cart-item");
      const id = item?.dataset.id;
      if (!id) return;

      if (!confirm("Bạn có chắc muốn xóa sản phẩm này không?")) return;

      try {
        const res = await fetch("index.php?controller=cart&action=remove", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `product_id=${id}`,
        });
        const data = await res.json();

        if (data.success) {
          item.remove();
          alert("🗑️ Đã xóa sản phẩm khỏi giỏ hàng.");
          location.reload();
        } else {
          alert(data.message || "Không thể xóa sản phẩm này.");
        }
      } catch (err) {
        console.error("❌ Lỗi khi xóa sản phẩm:", err);
      }
    });
  });

  // ===== Tăng / Giảm Số Lượng =====
  document.querySelectorAll(".cart-item").forEach((item) => {
    const minusBtn = item.querySelector(".qty-btn.minus");
    const plusBtn = item.querySelector(".qty-btn.plus");
    const qtyDisplay = item.querySelector("span");
    const id = item.dataset.id;

    if (!id) return;

    minusBtn?.addEventListener("click", () =>
      updateQuantity(id, parseInt(qtyDisplay.textContent) - 1)
    );
    plusBtn?.addEventListener("click", () =>
      updateQuantity(id, parseInt(qtyDisplay.textContent) + 1)
    );

    async function updateQuantity(productId, newQty) {
      if (newQty < 1) newQty = 1;

      try {
        const res = await fetch("index.php?controller=cart&action=update", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `product_id=${productId}&quantity=${newQty}`,
        });

        const data = await res.json();
        if (data.success) {
          qtyDisplay.textContent = newQty;
          location.reload();
        } else {
          alert(data.message || "Không thể cập nhật số lượng.");
        }
      } catch (err) {
        console.error("❌ Lỗi khi cập nhật số lượng:", err);
      }
    }
  });
});
