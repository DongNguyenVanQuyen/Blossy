// ====== Hàm cập nhật badge header ======
async function updateHeaderCounts() {
  try {
    const res = await fetch(
      "index.php?controller=headercount&action=getCounts"
    );
    const data = await res.json();

    if (!data.success) return;

    // Xóa badge cũ
    document
      .querySelectorAll("#favourite-Cart .badge")
      .forEach((el) => el.remove());

    // Yêu thích
    if (data.favorites > 0) {
      const favIcon = document.querySelector("#favourite-Cart .fa-heart");
      if (favIcon)
        favIcon.insertAdjacentHTML(
          "afterend",
          `<span class="badge">${data.favorites}</span>`
        );
    }

    // 🛒 Giỏ hàng
    if (data.cart > 0) {
      const cartIcon = document.querySelector(
        "#favourite-Cart .fa-cart-shopping"
      );
      if (cartIcon)
        cartIcon.insertAdjacentHTML(
          "afterend",
          `<span class="badge">${data.cart}</span>`
        );
    }
  } catch (err) {
    console.error("❌ Lỗi cập nhật badge header:", err);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const notifyIcon = document.querySelector(".notify-icon");
  const panel = document.querySelector(".notify-panel");
  const notifyBadge = document.querySelector(".notify-wrapper .badge");

  if (notifyIcon && panel) {
    // Bấm vào icon thì mở / đóng
    notifyIcon.addEventListener("click", (e) => {
      e.stopPropagation(); // tránh click lan ra ngoài
      panel.classList.toggle("active");
    });

    // Bấm ra ngoài để đóng
    document.addEventListener("click", (e) => {
      if (!panel.contains(e.target) && !notifyIcon.contains(e.target)) {
        panel.classList.remove("active");
      }
    });
  }
  if (notifyIcon) {
    notifyIcon.addEventListener("click", async () => {
      try {
        const res = await fetch(
          "index.php?controller=notification&action=markAll"
        );
        const data = await res.json();

        if (data.success && notifyBadge) {
          notifyBadge.remove(); // ẩn số thông báo sau khi đọc
        }
      } catch (err) {
        console.error("Lỗi đánh dấu đã đọc:", err);
      }
    });
  }
});
