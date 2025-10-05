document.addEventListener("DOMContentLoaded", function () {
  // ====== ĐỔI ẢNH CHÍNH KHI CLICK THUMBNAIL ======
  const mainImage = document.querySelector(".main-image");
  const thumbnails = document.querySelectorAll(".thumbnail-list img");

  thumbnails.forEach((thumb) => {
    thumb.addEventListener("click", function () {
      mainImage.src = this.src;
    });
  });

  // ====== TĂNG / GIẢM SỐ LƯỢNG ======
  const quantityInput = document.getElementById("input_quantity");

  // Tìm 2 nút tăng/giảm thông qua vị trí trong DOM
  const quantityContainer = document.querySelector(".quantity");
  const decreaseBtn = quantityContainer.querySelectorAll("button")[0];
  const increaseBtn = quantityContainer.querySelectorAll("button")[1];

  decreaseBtn.addEventListener("click", function () {
    let value = parseInt(quantityInput.value);
    if (value > 1) {
      quantityInput.value = value - 1;
    }
  });

  increaseBtn.addEventListener("click", function () {
    let value = parseInt(quantityInput.value);
    quantityInput.value = value + 1;
  });
});
