// ===============================
// FILTER SIDEBAR + TAG SYSTEM (AJAX)
// ===============================
document.addEventListener("DOMContentLoaded", function () {
  // Chỉ thay nội dung trong .product-grid
  const productGrid = document.querySelector("#product-list .product-grid");
  const filterForm = document.getElementById("filter-form");
  const tagContainer = document.querySelector(".tags-list"); // khớp với List.php
  const clearAllBtn = tagContainer?.querySelector(".clear-all");

  // ======== Tạo 1 tag ========
  function createTag(group, value, title, label) {
    if (!tagContainer) return;
    const tag = document.createElement("span");
    tag.className = "tag";
    tag.dataset.type = group;
    tag.dataset.value = value;
    tag.innerHTML = `${title}: ${label} <a href="#" class="remove-tag">×</a>`;
    if (clearAllBtn) tagContainer.insertBefore(tag, clearAllBtn);
    else tagContainer.appendChild(tag);
  }

  // ======== Cập nhật toàn bộ tag ========
  function updateTags() {
    if (!tagContainer) return;
    // xoá hết tag cũ
    tagContainer.querySelectorAll(".tag").forEach((t) => t.remove());

    // Loại hoa
    document
      .querySelectorAll("input[name='category[]']:checked")
      .forEach((cb) => {
        if (cb.value !== "all") {
          const label = cb.parentElement.textContent.trim();
          createTag("category", cb.value, "Flower", label);
        }
      });

    // Màu sắc
    document.querySelectorAll("input[name='color[]']:checked").forEach((cb) => {
      const label = cb.parentElement.textContent.trim();
      createTag("color", cb.value, "Color", label);
    });

    // Giá
    const priceSelect = document.querySelector("select[name='price_range']");
    if (priceSelect && priceSelect.value) {
      const text = priceSelect.options[priceSelect.selectedIndex].text;
      createTag("price", priceSelect.value, "Price", text);
    }
  }

  // ======== Gửi AJAX để lấy danh sách sản phẩm (chỉ HTML thẻ card) ========
  function sendAjaxFilter() {
    if (!filterForm || !productGrid) return;
    const formData = new FormData(filterForm);

    fetch(BASE_URL + "index.php?controller=products&action=filter", {
      method: "POST",
      body: formData,
    })
      .then((res) => {
        if (!res.ok) throw new Error("Network error");
        return res.text(); // Server trả về HTML (các _ProductCard)
      })
      .then((html) => {
        productGrid.innerHTML = html; // ✅ chỉ thay phần grid
      })
      .catch((err) => console.error("Lỗi AJAX:", err));
  }

  // ======== Sự kiện thay đổi filter ========
  filterForm?.querySelectorAll("input, select").forEach((el) => {
    el.addEventListener("change", () => {
      updateTags();
      sendAjaxFilter();
    });
  });

  // ======== Click xoá từng tag ========
  tagContainer?.addEventListener("click", (e) => {
    if (!e.target.classList.contains("remove-tag")) return;
    e.preventDefault();
    const tag = e.target.closest(".tag");
    const type = tag.dataset.type;
    const val = tag.dataset.value;

    if (type === "category" || type === "color") {
      document
        .querySelectorAll(`input[name='${type}[]'][value='${val}']`)
        .forEach((cb) => (cb.checked = false));
    } else if (type === "price") {
      const sel = document.querySelector("select[name='price_range']");
      if (sel) sel.value = "";
    }

    tag.remove();
    sendAjaxFilter();
  });

  // ======== Click "Clear All" ========
  clearAllBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    document
      .querySelectorAll("#filter-form input[type='checkbox']")
      .forEach((cb) => (cb.checked = false));
    const sel = document.querySelector("select[name='price_range']");
    if (sel) sel.value = "";
    tagContainer.querySelectorAll(".tag").forEach((t) => t.remove());
    sendAjaxFilter();
  });

  // ======== Lần đầu load ========
  updateTags();
});
