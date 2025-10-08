document.addEventListener("DOMContentLoaded", () => {
  // Hiệu ứng fade in
  document.querySelector(".order-completed").classList.add("visible");

  // Xóa session last_order sau khi vào trang
  fetch("index.php?controller=order&action=clearSession", { method: "POST" });

  // Tự động quay về Shop sau 15s
  setTimeout(() => {
    window.location.href = "index.php?controller=products&action=index";
  }, 15000);
});
