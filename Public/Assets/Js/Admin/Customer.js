document.querySelectorAll(".customer-btn-toggle").forEach((btn) => {
  btn.addEventListener("click", async () => {
    const id = btn.dataset.id;
    const res = await fetch(
      "index.php?controller=admincustomer&action=toggle",
      {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + id,
      }
    );
    const data = await res.json();
    if (data.success) {
      showToast("Cập nhật trạng thái thành công!", "success");
      setTimeout(() => location.reload(), 500);
    } else {
      showToast("Lỗi khi cập nhật trạng thái.", "error");
    }
  });
});
