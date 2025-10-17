document.addEventListener("DOMContentLoaded", () => {
  const currentUrl = window.location.href;
  const links = document.querySelectorAll(".admin-sidebar nav a");

  links.forEach((link) => {
    const linkUrl = link.href;

    // Xóa class active cũ
    link.classList.remove("active");

    // So khớp controller hiện tại trong URL
    const currentParams = new URL(currentUrl).searchParams.get("controller");
    const linkParams = new URL(linkUrl).searchParams.get("controller");

    if (currentParams && linkParams && currentParams === linkParams) {
      link.classList.add("active");
    }
  });
});
