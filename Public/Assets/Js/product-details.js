// Product - details
document.addEventListener("DOMContentLoaded", () => {
  // ===== THAY ẢNH CHÍNH KHI CLICK ẢNH PHỤ =====
  const gallery = document.querySelector(
    ".product-detail-container .product-gallery"
  );
  if (gallery) {
    const mainImage = gallery.querySelector(".main-image");
    const thumbnails = gallery.querySelectorAll(".thumbnail-list img");

    thumbnails.forEach((thumb) => {
      thumb.addEventListener("click", () => {
        thumbnails.forEach((img) => img.classList.remove("active"));
        thumb.classList.add("active");
        mainImage.src = thumb.src;
      });
    });
  }
});

// ===== XỬ LÝ TĂNG GIẢM SỐ LƯỢNG =====
document.addEventListener("DOMContentLoaded", () => {
  // ===== XỬ LÝ TĂNG GIẢM SỐ LƯỢNG =====
  const qty = document.querySelector(".product-info .actions .quantity");

  if (qty) {
    console.log(" co qty");
    const btnMinus = qty.querySelector("button:first-child");
    const btnPlus = qty.querySelector("button:last-child");
    const input = qty.querySelector("#input_quantity");

    btnMinus.addEventListener("click", () => {
      let current = parseInt(input.value);
      if (current > 1) input.value = current - 1;
    });

    btnPlus.addEventListener("click", () => {
      let current = parseInt(input.value);
      input.value = current + 1;
    });
  } else {
    console.log("ko co qty");
  }
});
