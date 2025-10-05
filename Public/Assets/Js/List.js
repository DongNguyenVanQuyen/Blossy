// ===============================
// FILTER SIDEBAR + TAG SYSTEM (AJAX)
// ===============================
document.addEventListener("DOMContentLoaded", function () {
  const shopResults = document.getElementById("product-list"); // nơi hiển thị danh sách sản phẩm
  const filterForm = document.getElementById("filter-form"); // form lọc
  const tagContainer = document.querySelector(".tags-list"); // nơi hiển thị các tag filter
  const clearAllBtn = tagContainer?.querySelector(".clear-all"); // nút Clear All

  // ======== Hàm tạo tag hiển thị ========
  function createTag(group, value, title, label) {
    const tag = document.createElement("span");
    tag.className = "tag";
    tag.dataset.type = group;
    tag.dataset.value = value;
    tag.innerHTML = `${title}: ${label} <a href="#" class="remove-tag">×</a>`;
    if (clearAllBtn) {
      tagContainer.insertBefore(tag, clearAllBtn);
    } else {
      tagContainer.appendChild(tag);
    }
  }

  // ======== Hàm cập nhật danh sách tag ========
  function updateTags() {
    if (!tagContainer) return;
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
    const val = priceSelect?.value;
    if (val) {
      const text = priceSelect.options[priceSelect.selectedIndex].text;
      createTag("price", val, "Price", text);
    }
  }

  // ======== Gửi AJAX để lọc sản phẩm ========
  function sendAjaxFilter() {
    if (!filterForm || !shopResults) return;
    const formData = new FormData(filterForm);

    fetch(BASE_URL + "index.php?controller=products&action=filter", {
      method: "POST",
      body: formData,
    })
      .then((res) => {
        if (!res.ok) throw new Error("Network error");
        return res.text(); // ✅ Nhận HTML (không phải JSON)
      })
      .then((html) => {
        shopResults.innerHTML = html;
      })
      .catch((err) => console.error("Lỗi AJAX:", err));
  }

  // ======== Xử lý sự kiện tick / chọn giá ========
  document
    .querySelectorAll("#filter-form input[type='checkbox']")
    .forEach((cb) => {
      cb.addEventListener("change", () => {
        updateTags();
        sendAjaxFilter();
      });
    });

  document
    .querySelector("#filter-form select[name='price_range']")
    ?.addEventListener("change", () => {
      updateTags();
      sendAjaxFilter();
    });

  // ======== Click ❌ trên tag ========
  tagContainer?.addEventListener("click", (e) => {
    if (e.target.classList.contains("remove-tag")) {
      e.preventDefault();
      const tag = e.target.closest(".tag");
      const type = tag.dataset.type;
      const val = tag.dataset.value;

      // Bỏ tick checkbox tương ứng
      if (type === "category" || type === "color") {
        document
          .querySelectorAll(`input[name='${type}[]'][value='${val}']`)
          .forEach((cb) => (cb.checked = false));
      } else if (type === "price") {
        document.querySelector("select[name='price_range']").value = "";
      }

      tag.remove();
      sendAjaxFilter();
    }
  });

  // ======== Click "Xóa tất cả" ========
  clearAllBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    document
      .querySelectorAll("#filter-form input[type='checkbox']")
      .forEach((cb) => (cb.checked = false));
    document.querySelector("select[name='price_range']").value = "";
    tagContainer.querySelectorAll(".tag").forEach((tag) => tag.remove());
    sendAjaxFilter();
  });

  // ======== Lần đầu load: hiển thị tag đúng với dữ liệu đã chọn ========
  updateTags();
});
