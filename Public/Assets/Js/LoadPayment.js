document.addEventListener("DOMContentLoaded", () => {
  const buyBtn = document.querySelector(".checkout-now");

  if (buyBtn) {
    buyBtn.addEventListener("click", () => {
      // ✅ Chuyển đến trang thanh toán
      window.location.href = "index.php?controller=checkout&action=index";
    });
  }
});
