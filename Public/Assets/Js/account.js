// Xử lý chuyển tab
document.querySelectorAll(".my-account__tab").forEach((tab) => {
  tab.addEventListener("click", function () {
    // Xóa active ở tất cả các tab
    document
      .querySelectorAll(".my-account__tab")
      .forEach((t) => t.classList.remove("my-account__tab--active"));

    // Ẩn toàn bộ panel
    document
      .querySelectorAll(".my-account__panel")
      .forEach((p) => p.classList.remove("active"));

    // Thêm active cho tab được bấm
    this.classList.add("my-account__tab--active");

    // Hiện panel tương ứng
    const target = this.getAttribute("data-tab");
    document.getElementById(target)?.classList.add("active");
  });
});

// HÀM SỬA ĐỊA CHỈ
function editAddress(id, address) {
  const inputId = document.getElementById("addressId");
  const textarea = document.getElementById("addressInput");
  const btn = document.getElementById("saveBtn");

  if (!inputId || !textarea || !btn) {
    alert("Không tìm thấy form địa chỉ để chỉnh sửa!");
    return;
  }

  inputId.value = id;
  textarea.value = address;
  btn.textContent = "Cập Nhật Địa Chỉ";

  // Cuộn xuống phần form khi bấm sửa
  document.getElementById("address").scrollIntoView({ behavior: "smooth" });
}
