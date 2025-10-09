document.addEventListener("DOMContentLoaded", () => {
  const buyBtn = document.querySelector(".checkout-now");

  if (buyBtn) {
    buyBtn.addEventListener("click", async () => {
      const productId = buyBtn.dataset.id;
      const qtyInput = document.getElementById("input_quantity");
      const quantity = parseInt(qtyInput?.value || "1");

      try {
        // ✅ Gửi request tạo đơn tạm "mua ngay"
        const res = await fetch("index.php?controller=checkout&action=buyNow", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `product_id=${productId}&quantity=${quantity}`,
        });

        const data = await res.json();

        if (data.success) {
          // ✅ Chuyển đến trang checkout với 1 sản phẩm duy nhất
          window.location.href = "index.php?controller=checkout&action=index";
        } else {
          alert(data.message || "Không thể xử lý mua ngay");
        }
      } catch (err) {
        console.error("❌ Lỗi mua ngay:", err);
        alert("Có lỗi xảy ra, vui lòng thử lại.");
      }
    });
  }
});
