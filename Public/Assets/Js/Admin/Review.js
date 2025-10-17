document.querySelectorAll(".btn-toggle").forEach((btn) => {
  btn.addEventListener("click", async () => {
    const row = btn.closest("tr");
    const id = row.dataset.id;
    const status = btn.dataset.status;

    try {
      const res = await fetch(
        "index.php?controller=adminreview&action=toggleVisibility",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${id}&is_approved=${status}`,
        }
      );

      const data = await res.json();

      if (data.success) {
        showToast(data.message || "Cập nhật trạng thái thành công", "success");
      } else {
        showToast(data.message || "Cập nhật thất bại", "error");
      }

      setTimeout(() => location.reload(), 1000);
    } catch (err) {
      showToast("Lỗi kết nối đến máy chủ", "error");
    }
  });
});

document.querySelectorAll(".btn-delete").forEach((btn) => {
  btn.addEventListener("click", async () => {
    if (!confirm("Bạn có chắc muốn xóa review này?")) return;

    const id = btn.closest("tr").dataset.id;

    try {
      const res = await fetch(
        "index.php?controller=adminreview&action=delete",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${id}`,
        }
      );

      const data = await res.json();

      if (data.success) {
        showToast(data.message || "Đã xóa đánh giá", "success");
      } else {
        showToast(data.message || "Không thể xóa đánh giá", "error");
      }

      setTimeout(() => location.reload(), 1000);
    } catch (err) {
      showToast("Lỗi khi kết nối máy chủ", "error");
    }
  });
});
