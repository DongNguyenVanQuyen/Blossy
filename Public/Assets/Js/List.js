// ===============================
// FILTER SIDEBAR + TAG SYSTEM (AJAX)
// ===============================
document.addEventListener("DOMContentLoaded", function () {
  // ====== Lấy các phần tử cần dùng ======
  const productGrid = document.querySelector("#product-list .product-grid");
  const paginationContainer = document.querySelector("#pagination");
  const filterForm = document.getElementById("filter-form");
  const tagContainer = document.querySelector(".tags-list");
  const clearAllBtn = tagContainer?.querySelector(".clear-all");

  // ====== Loading spinner ======
  const loadingSpinner = document.createElement("div");
  loadingSpinner.className = "loading-spinner";
  loadingSpinner.style.display = "none";
  productGrid.parentElement.appendChild(loadingSpinner);

  // ====== Tạo 1 tag ======
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

  // ====== Cập nhật toàn bộ tag ======
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
    if (priceSelect && priceSelect.value) {
      const text = priceSelect.options[priceSelect.selectedIndex].text;
      createTag("price", priceSelect.value, "Price", text);
    }
  }

  // ====== Hàm gửi AJAX để lấy sản phẩm ======
  function sendAjaxFilter(page = 1) {
    if (!filterForm || !productGrid) return;

    const formData = new FormData(filterForm);
    formData.append("page", page);

    // Hiển thị loading
    loadingSpinner.style.display = "block";
    productGrid.style.opacity = "0.6";
    paginationContainer.style.opacity = "0.6";

    fetch(BASE_URL + "index.php?controller=Products&action=filter", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.text())
      .then((text) => {
        let data;
        try {
          data = JSON.parse(text); // ✅ Parse JSON thật sự
        } catch (err) {
          console.error("Server không trả JSON hợp lệ:", text);
          throw new Error("Server không trả về JSON hợp lệ");
        }

        if (data.error) throw new Error(data.error);

        // ===== Cập nhật HTML sản phẩm =====
        if (data.totalProducts > 0 && data.productsHtml?.trim()) {
          productGrid.innerHTML = data.productsHtml;
        } else {
          productGrid.innerHTML =
            '<div class="no-products">Không tìm thấy sản phẩm nào</div>';
        }

        // ===== Cập nhật phân trang =====
        if (data.paginationHtml) {
          paginationContainer.innerHTML = data.paginationHtml;
          setupPagination();
        } else {
          paginationContainer.innerHTML = "";
        }

        // ===== Cập nhật URL =====
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set("page", data.currentPage);
        newUrl.searchParams.delete("category[]");
        newUrl.searchParams.delete("color[]");
        newUrl.searchParams.delete("price_range");

        formData
          .getAll("category[]")
          .forEach((v) => newUrl.searchParams.append("category[]", v));
        formData
          .getAll("color[]")
          .forEach((v) => newUrl.searchParams.append("color[]", v));
        const price = formData.get("price_range");
        if (price) newUrl.searchParams.set("price_range", price);

        window.history.pushState(data, "", newUrl);

        // ===== Cập nhật số lượng sản phẩm =====
        const totalProductsEl = document.querySelector(".total-products");
        if (totalProductsEl)
          totalProductsEl.textContent = `Tìm thấy ${data.totalProducts} sản phẩm`;
      })
      .catch((error) => {
        console.error("Lỗi:", error);
        productGrid.innerHTML = `<div class="error-message">Có lỗi xảy ra: ${error.message}</div>`;
      })
      .finally(() => {
        loadingSpinner.style.display = "none";
        productGrid.style.opacity = "1";
        paginationContainer.style.opacity = "1";
      });
  }

  // ====== CHẶN SUBMIT FORM mặc định ======
  filterForm?.addEventListener("submit", function (e) {
    e.preventDefault();
    updateTags();
    sendAjaxFilter(1);
    const newUrl = new URL(window.location.href);
    newUrl.searchParams.delete("page");
    window.history.pushState({}, "", newUrl);
  });

  // ====== Sự kiện thay đổi filter ======
  // filterForm?.querySelectorAll("input, select").forEach((el) => {
  //   el.addEventListener("change", () => {
  //     updateTags();
  //     sendAjaxFilter(1);
  //     const newUrl = new URL(window.location.href);
  //     newUrl.searchParams.delete("page");
  //     window.history.pushState({}, "", newUrl);
  //   });
  // });

  // ====== Xoá từng tag ======
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
    sendAjaxFilter(1); // luôn load lại trang đầu

    const newUrl = new URL(window.location.href);
    newUrl.searchParams.delete("page");
    window.history.pushState({}, "", newUrl);
  });

  // ====== Click "Clear All" ======
  clearAllBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    document
      .querySelectorAll("#filter-form input[type='checkbox']")
      .forEach((cb) => (cb.checked = false));
    const sel = document.querySelector("select[name='price_range']");
    if (sel) sel.value = "";
    tagContainer.querySelectorAll(".tag").forEach((t) => t.remove());
    sendAjaxFilter(1); // luôn load lại trang đầu

    const newUrl = new URL(window.location.href);
    newUrl.searchParams.delete("page");
    window.history.pushState({}, "", newUrl);
  });
  if (tagContainer && !tagContainer.querySelectorAll(".tag").length) {
    const allCheckbox = document.querySelector(
      `input[name='category[]'][value='all']`
    );
    if (allCheckbox) allCheckbox.checked = true;
  }

  // ====== Xử lý phân trang ======
  function setupPagination() {
    const paginationLinks = document.querySelectorAll(".pagination-link");
    paginationLinks.forEach((link) => {
      link.addEventListener("click", handlePaginationClick);
    });
  }

  function handlePaginationClick(e) {
    e.preventDefault();
    const page = this.dataset.page;
    if (page) {
      sendAjaxFilter(parseInt(page));
      const shopContainer = document.querySelector(".shop-container");
      if (shopContainer) shopContainer.scrollIntoView({ behavior: "smooth" });
    }
  }

  // ====== Back / Forward ======
  window.addEventListener("popstate", (event) => {
    if (event.state) {
      productGrid.innerHTML = event.state.productsHtml || "";
      if (event.state.paginationHtml) {
        paginationContainer.innerHTML = event.state.paginationHtml;
        setupPagination();
      }
      updateTags();
    }
  });

  // ====== Lần đầu load ======
  updateTags();
  setupPagination();
});
