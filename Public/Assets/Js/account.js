document.querySelectorAll(".my-account__tab").forEach((tab) => {
  tab.addEventListener("click", function () {
    document
      .querySelectorAll(".my-account__tab")
      .forEach((t) => t.classList.remove("active"));
    document
      .querySelectorAll(".my-account__panel")
      .forEach((p) => p.classList.remove("active"));

    this.classList.add("active");
    const target = this.getAttribute("data-tab");
    document.getElementById(target).classList.add("active");
  });
});
