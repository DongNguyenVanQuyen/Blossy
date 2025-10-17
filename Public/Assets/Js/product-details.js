document.addEventListener("DOMContentLoaded", function () {
  // ====== ĐỔI ẢNH CHÍNH KHI CLICK THUMBNAIL ======
  const mainImage = document.querySelector(".main-image");
  const thumbnails = document.querySelectorAll(".thumbnail-list img");

  thumbnails.forEach((thumb) => {
    thumb.addEventListener("click", function () {
      mainImage.src = this.src;
    });
  });
});

// kiểm tra hàng và thiết lập mua
document.addEventListener("DOMContentLoaded", function () {
  const quantityInput = document.getElementById("input_quantity");
  const decreaseBtn = document.querySelector(".btn-decrease");
  const increaseBtn = document.querySelector(".btn-increase");
  const addBtn = document.querySelector(".btn.add");
  const buyBtn = document.querySelector(".btn.buy");

  const stock = parseInt(quantityInput?.dataset.stock || 0);

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
      showToast("Không thể vượt quá số lượng tồn kho!", "warning");
    }
  });

  // ====== Thêm yêu thích ======
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
            showToast(data.message, "success");

            // Cập nhật badge header
            updateHeaderCounts();
          } else {
            showToast(data.message || "Lỗi không xác định", "error");
          }
        })
        .catch((err) => {
          console.error("Lỗi yêu thích:", err);
          showToast("Kết nối thất bại!", "error");
        });
    });
  }
});

// ====== THÊM VÀO GIỎ HÀNG ======
document.addEventListener("DOMContentLoaded", function () {
  const addButtons = document.querySelectorAll(".add-to-cart");
  const quantityInput = document.getElementById("input_quantity");
  let isAdding = false; // chống double click

  addButtons.forEach((btn) => {
    btn.addEventListener("click", async () => {
      if (isAdding) return;
      isAdding = true;
      btn.disabled = true;

      const id = btn.dataset.id;

      //Lấy số lượng từ input (nếu có), ngược lại mặc định 1
      const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;

      try {
        const res = await fetch("index.php?controller=cart&action=add", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            product_id: id,
            quantity: quantity,
          }),
        });

        const data = await res.json();

        if (data.success) {
          showToast(`Đã thêm ${quantity} sản phẩm vào giỏ hàng`, "success");

          // Cập nhật badge header
          updateHeaderCounts();
        } else {
          showToast(data.message, "error");

          // 🔹 Nếu chưa đăng nhập, chuyển hướng sau 1.5s
          if (data.message.includes("đăng nhập")) {
            setTimeout(() => {
              window.location.href = "index.php?controller=auth&action=login";
            }, 1500);
          }
        }
      } catch (err) {
        showToast("Đã xảy ra lỗi, vui lòng thử lại!", "error");
      } finally {
        isAdding = false;
        btn.disabled = false;
      }
    });
  });
});
