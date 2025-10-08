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
          this.closest(".wishlist__row").remove();
        }
      })
      .catch((err) => console.error("Lỗi xóa yêu thích:", err));
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
        showToast("Đã thêm vào giỏ hàng!", "success");
      } else {
        showToast(data.message || "Không thể thêm vào giỏ hàng", "error");
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
  const toast = document.createElement("div");
  toast.className = `toast ${type}`;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => toast.classList.add("show"), 100);
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 300);
  }, 2000);
}
