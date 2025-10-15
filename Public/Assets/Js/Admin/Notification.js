document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("createNoticeForm");
  const idField = document.getElementById("notice_id");
  const titleField = document.getElementById("notice_title");
  const bodyField = document.getElementById("notice_body");
  const formTitle = document.getElementById("formTitle");
  const cancelEdit = document.getElementById("cancelEdit");

  // ✅ Thêm hoặc cập nhật
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = new FormData(form);

    const res = await fetch(
      "index.php?controller=adminnotification&action=save",
      {
        method: "POST",
        body: data,
      }
    );
    const json = await res.json();

    if (json.success) {
      alert(
        json.updated ? "Cập nhật thành công!" : "Tạo thông báo thành công!"
      );
      window.location.reload();
    } else {
      alert(json.message || "Lỗi khi lưu!");
    }
  });

  // ✅ Xử lý sửa (load dữ liệu vào form)
  document.querySelectorAll(".btn-edit").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = btn.dataset.id;
      const res = await fetch(
        `index.php?controller=adminnotification&action=getById&id=${id}`
      );
      const data = await res.json();

      if (data.success) {
        idField.value = data.data.id;
        titleField.value = data.data.title;
        bodyField.value = data.data.body;
        formTitle.textContent = "Sửa thông báo";
        cancelEdit.style.display = "inline-block";
      }
    });
  });

  // Hủy sửa
  cancelEdit.addEventListener("click", () => {
    idField.value = "";
    titleField.value = "";
    bodyField.value = "";
    formTitle.textContent = "Thêm thông báo mới";
    cancelEdit.style.display = "none";
  });

  // Xóa
  document.querySelectorAll(".btn-delete").forEach((btn) => {
    btn.addEventListener("click", async () => {
      if (!confirm("Bạn có chắc muốn xóa thông báo này?")) return;
      const id = btn.dataset.id;

      const res = await fetch(
        "index.php?controller=adminnotification&action=delete",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${id}`,
        }
      );
      const json = await res.json();
      if (json.success) {
        alert(" Đã xóa thành công!");
        btn.closest("tr").remove();
      } else {
        alert(json.message || "Lỗi khi xóa!");
      }
    });
  });
});
