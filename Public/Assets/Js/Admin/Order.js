document.querySelectorAll(".order-btn-update").forEach((btn) => {
  btn.addEventListener("click", async () => {
    const id = btn.dataset.id;
    const select = document.querySelector(
      `.order-status-select[data-id='${id}']`
    );
    const newStatus = select.value;

    const statusMap = {
      cho_xac_nhan: { text: "Chờ xác nhận", cls: "pending" },
      dang_giao: { text: "Đang giao", cls: "shipping" },
      hoan_thanh: { text: "Hoàn thành", cls: "success" },
      huy: { text: "Đã hủy", cls: "cancel" },
    };

    // ✅ Chặn click liên tục
    btn.disabled = true;
    btn.textContent = "Đang lưu...";

    try {
      const res = await fetch(
        "index.php?controller=adminorder&action=updateStatus",
        {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(
            newStatus
          )}`,
        }
      );

      const data = await res.json();

      if (!data.success) {
        showToast(data.message || "Lỗi khi cập nhật trạng thái!", "error");
        return;
      }

      // ✅ Cập nhật giao diện tức thì
      const row = btn.closest("tr");
      const badge = row.querySelector(".order-status");

      if (badge && statusMap[newStatus]) {
        badge.classList.remove(
          "pending",
          "shipping",
          "success",
          "cancel",
          "unknown"
        );
        badge.classList.add(statusMap[newStatus].cls);
        badge.textContent = statusMap[newStatus].text;
      }

      // ✅ Ưu tiên dùng dữ liệu trả từ server nếu có
      if (data.displayText && data.displayClass && badge) {
        badge.classList.remove(
          "pending",
          "shipping",
          "success",
          "cancel",
          "unknown"
        );
        badge.classList.add(data.displayClass);
        badge.textContent = data.displayText;
      }

      showToast("Cập nhật trạng thái thành công.", "success");
    } catch (e) {
      console.error(e);
      showToast("Lỗi mạng khi cập nhật trạng thái.", "error");
    } finally {
      btn.disabled = false;
      btn.textContent = "Cập nhật";
    }
  });
});
