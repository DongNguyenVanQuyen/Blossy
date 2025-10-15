// 🟢 Khi bấm "Sửa"
document.querySelectorAll(".promotion-edit").forEach((btn) => {
  btn.addEventListener("click", () => {
    // Đổ dữ liệu xuống form
    document.getElementById("promotionFormTitle").textContent =
      "Cập nhật Khuyến Mãi";
    document.getElementById("promotionSubmitBtn").textContent = "Lưu Thay Đổi";
    document.getElementById("promotionCancelBtn").style.display =
      "inline-block";
    document.getElementById("promotionForm").action =
      "index.php?controller=adminpromotion&action=edit";

    document.getElementById("promotion_id").value = btn.dataset.id;
    document.getElementById("promotion_name").value = btn.dataset.name;
    document.getElementById("promotion_code").value = btn.dataset.code;
    document.getElementById("promotion_discount").value = btn.dataset.discount;
    document.getElementById("promotion_start").value =
      btn.dataset.start.replace(" ", "T");
    document.getElementById("promotion_end").value = btn.dataset.end.replace(
      " ",
      "T"
    );
    document.getElementById("promotion_active").checked =
      btn.dataset.active == 1;

    // Cuộn xuống form
    document
      .querySelector(".admin-promotion__form")
      .scrollIntoView({ behavior: "smooth" });
  });
});

// 🔴 Hủy chỉnh sửa
document.getElementById("promotionCancelBtn").addEventListener("click", () => {
  document.getElementById("promotionForm").reset();
  document.getElementById("promotionFormTitle").textContent =
    "Thêm khuyến mãi mới";
  document.getElementById("promotionSubmitBtn").textContent =
    "+ Thêm Khuyến Mãi";
  document.getElementById("promotionCancelBtn").style.display = "none";
  document.getElementById("promotionForm").action =
    "index.php?controller=adminpromotion&action=create";
});

// 🟢 Toggle kích hoạt
document.querySelectorAll(".promotion-toggle").forEach((btn) => {
  btn.addEventListener("click", async () => {
    const id = btn.dataset.id;
    const res = await fetch(
      "index.php?controller=adminpromotion&action=toggle",
      {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + id,
      }
    );
    const data = await res.json();
    if (data.success) location.reload();
  });
});
