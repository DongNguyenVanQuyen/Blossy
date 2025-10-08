document.addEventListener("DOMContentLoaded", function () {
  // ====== ĐỔI ẢNH CHÍNH KHI CLICK THUMBNAIL ======
  const mainImage = document.querySelector(".main-image");
  const thumbnails = document.querySelectorAll(".thumbnail-list img");

  thumbnails.forEach((thumb) => {
    thumb.addEventListener("click", function () {
      mainImage.src = this.src;
    });
  });

  // ====== THÊM SẢN PHẨM VÀO YÊU THÍCH ======
  const favoriteBtn = document.querySelector(".favorite-btn");
  console.log("favoriteBtn:", favoriteBtn);
  if (favoriteBtn) {
    favoriteBtn.addEventListener("click", function () {
      const productId = this.dataset.productId;
      const icon = this.querySelector("i");

      fetch(`index.php?controller=favorites&action=toggle`, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `product_id=${productId}`,
      })
        .then((res) => res.text())
        .then((text) => {
          console.log("=== RAW RESPONSE START ===");
          console.log(text);
          console.log("=== RAW RESPONSE END ===");

          try {
            const data = JSON.parse(text);
            if (data.success) {
              icon.classList.toggle("active", data.favorited);
            } else {
              alert(data.message || "Lỗi không xác định");
            }
          } catch (e) {
            console.error("JSON parse error:", e);
            alert("Phản hồi không phải JSON hợp lệ! Xem console để biết thêm.");
          }
        })
        .catch((err) => console.error("Lỗi yêu thích:", err));
    });
  }
});

// kiểm tra hàng và thiết lặp mua
document.addEventListener("DOMContentLoaded", function () {
  const quantityInput = document.getElementById("input_quantity");
  const decreaseBtn = document.querySelector(".btn-decrease");
  const increaseBtn = document.querySelector(".btn-increase");
  const addBtn = document.querySelector(".btn.add");
  const buyBtn = document.querySelector(".btn.buy");

  const stock = parseInt(quantityInput.dataset.stock || 0);

  // === Nếu hết hàng thì disable toàn bộ ===
  if (stock <= 0) {
    quantityInput.value = 0;
    quantityInput.disabled = true;
    decreaseBtn.disabled = true;
    increaseBtn.disabled = true;
    addBtn.disabled = true;
    buyBtn.disabled = true;

    addBtn.classList.add("disabled");
    buyBtn.classList.add("disabled");
  }

  // === Giảm số lượng ===
  decreaseBtn?.addEventListener("click", function () {
    let value = parseInt(quantityInput.value);
    if (value > 1) quantityInput.value = value - 1;
  });

  // === Tăng số lượng (giới hạn max = stock) ===
  increaseBtn?.addEventListener("click", function () {
    let value = parseInt(quantityInput.value);
    if (value < stock) {
      quantityInput.value = value + 1;
    } else {
      alert("Không thể vượt quá số lượng tồn kho (" + stock + ")");
    }
  });

  // ====== Thêm yêu thích giữ nguyên ======
  const favoriteBtn = document.querySelector(".favorite-btn");
  if (favoriteBtn) {
    favoriteBtn.addEventListener("click", function () {
      const productId = this.dataset.productId;
      const icon = this.querySelector("i");

      fetch(`index.php?controller=favorites&action=toggle`, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `product_id=${productId}`,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            icon.classList.toggle("active", data.favorited);
          } else {
            alert(data.message || "Lỗi không xác định");
          }
        })
        .catch((err) => console.error("Lỗi yêu thích:", err));
    });
  }
});

// ====== THÊM VÀO GIỎ HÀNG ======
document.addEventListener("DOMContentLoaded", function () {
  const addButtons = document.querySelectorAll(".add-to-cart");
  const quantityInput = document.getElementById("input_quantity"); // ✅ thêm dòng này
  let isAdding = false; // chống double click

  addButtons.forEach((btn) => {
    btn.addEventListener("click", async () => {
      if (isAdding) return;
      isAdding = true;
      btn.disabled = true;

      const id = btn.dataset.id;

      // ✅ Lấy số lượng từ input (nếu có), ngược lại mặc định 1
      const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;

      try {
        const res = await fetch("index.php?controller=cart&action=add", {
          method: "POST",
          body: new URLSearchParams({
            product_id: id,
            quantity: quantity, // ✅ gửi đúng số lượng
          }),
        });

        const data = await res.json();

        if (data.success) {
          showToast(`Đã thêm ${quantity} sản phẩm vào giỏ hàng`, "success");
        } else {
          showToast(data.message, "error");
        }
      } catch (err) {
        showToast("Đã xảy ra lỗi, vui lòng thử lại!", "error");
      } finally {
        isAdding = false;
        btn.disabled = false;
      }
    });
  });

  // ===== Toast thông báo =====
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
});
