const modal = document.getElementById("voucherModal");
const addBtn = document.getElementById("addVoucherBtn");
const closeBtn = document.getElementById("closeModal");
const form = document.getElementById("voucherForm");
const title = document.getElementById("voucherModalTitle");

addBtn.onclick = () => {
  form.reset();
  form.action = "index.php?controller=adminvoucher&action=create";
  title.textContent = "Thêm Voucher";
  modal.style.display = "flex";
};

document.querySelectorAll(".voucher-edit").forEach((btn) => {
  btn.addEventListener("click", () => {
    modal.style.display = "flex";
    form.action = "index.php?controller=adminvoucher&action=edit";
    title.textContent = "Cập nhật Voucher";
    document.getElementById("voucherId").value = btn.dataset.id;
    document.getElementById("voucherCode").value = btn.dataset.code;
    document.getElementById("voucherType").value = btn.dataset.type;
    document.getElementById("voucherValue").value = btn.dataset.value;
    document.getElementById("voucherMax").value = btn.dataset.max;
    document.getElementById("voucherMin").value = btn.dataset.min;
    document.getElementById("voucherTotal").value = btn.dataset.total;
    document.getElementById("voucherLimit").value = btn.dataset.limit;
    document.getElementById("voucherStart").value =
      btn.dataset.start.split(" ")[0];
    document.getElementById("voucherEnd").value = btn.dataset.end.split(" ")[0];
    document.getElementById("voucherActive").checked = btn.dataset.active == 1;
  });
});

closeBtn.onclick = () => (modal.style.display = "none");

window.onclick = (e) => {
  if (e.target == modal) modal.style.display = "none";
};

document.querySelectorAll(".voucher-toggle").forEach((btn) => {
  btn.addEventListener("click", async () => {
    const id = btn.dataset.id;
    const res = await fetch("index.php?controller=adminvoucher&action=toggle", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "id=" + id,
    });
    const data = await res.json();
    if (data.success) {
      showToast("Cập nhật trạng thái thành công!", "success");
      setTimeout(() => location.reload(), 500);
    } else {
      showToast("Lỗi khi cập nhật trạng thái.", "error");
    }
  });
});
