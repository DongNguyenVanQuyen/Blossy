// ===============================
// FILTER SIDEBAR + TAG SYSTEM + SEARCH (AJAX)
// ===============================
document.addEventListener("DOMContentLoaded", function () {
  // ====== Lấy các phần tử cần dùng ======
  const searchInput = document.getElementById("Search-input");
  const searchBtn = document.getElementById("Search-btn");

  // ====== Hàm xử lý tìm kiếm chung ======
  function handleSearchRedirect() {
    const keyword = searchInput?.value.trim();
    if (!keyword) return;

    const currentUrl = new URL(window.location.href);
    const isShopPage =
      currentUrl.searchParams.get("controller") === "products" &&
      currentUrl.searchParams.get("action") === "index";

    // Nếu KHÔNG ở trang Shop → chuyển hướng
    if (!isShopPage) {
      const shopUrl =
        BASE_URL +
        "index.php?controller=products&action=index&keyword=" +
        encodeURIComponent(keyword);
      window.location.href = shopUrl;
      return;
    }

    // Nếu đang ở Shop → tìm kiếm bằng AJAX
    sendAjaxFilter(1);
  }

  // ====== Bắt sự kiện cho search ======
  searchInput?.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      handleSearchRedirect();
    }
  });

  searchBtn?.addEventListener("click", handleSearchRedirect);

  // ====== Bỏ qua nếu không có layout Shop ======
  const productGrid = document.querySelector("#product-list .product-grid");
  const paginationContainer = document.querySelector("#pagination");
  const filterForm = document.getElementById("filter-form");
  const tagContainer = document.querySelector(".tags-list");
  const clearAllBtn = tagContainer?.querySelector(".clear-all");
  if (!productGrid || !filterForm || !paginationContainer) return;

  let isSearching = false;

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

  // ====== Gửi AJAX lọc + tìm kiếm ======
  async function sendAjaxFilter(page = 1) {
    if (isSearching) return;
    isSearching = true;

    const formData = new FormData(filterForm);
    formData.append("page", page);

    const keyword = searchInput?.value.trim() || "";
    if (keyword) formData.append("keyword", keyword);

    loadingSpinner.style.display = "block";
    productGrid.style.opacity = "0.6";
    paginationContainer.style.opacity = "0.6";

    try {
      const res = await fetch(
        BASE_URL + "index.php?controller=Products&action=filter",
        {
          method: "POST",
          body: formData,
        }
      );

      if (!res.ok) throw new Error("Lỗi mạng hoặc server");

      const text = await res.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch {
        throw new Error("Phản hồi không hợp lệ từ server");
      }

      if (data.error) throw new Error(data.error);

      // Cập nhật sản phẩm
      if (data.totalProducts > 0 && data.productsHtml?.trim()) {
        productGrid.innerHTML = data.productsHtml;
      } else {
        productGrid.innerHTML =
          '<div class="no-products">Không tìm thấy sản phẩm phù hợp.</div>';
      }

      // Cập nhật phân trang
      if (data.paginationHtml) {
        paginationContainer.innerHTML = data.paginationHtml;
        setupPagination();
      } else {
        paginationContainer.innerHTML = "";
      }

      // Cập nhật URL
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
      if (keyword) newUrl.searchParams.set("keyword", keyword);
      else newUrl.searchParams.delete("keyword");

      window.history.pushState(data, "", newUrl);

      // Cập nhật số lượng sản phẩm
      const totalProductsEl = document.querySelector(".total-products");
      if (totalProductsEl)
        totalProductsEl.textContent = `Tìm thấy ${data.totalProducts} sản phẩm`;
    } catch (err) {
      console.error("Lỗi:", err);
      productGrid.innerHTML = `<div class="error-message">Có lỗi xảy ra: ${err.message}</div>`;
    } finally {
      loadingSpinner.style.display = "none";
      productGrid.style.opacity = "1";
      paginationContainer.style.opacity = "1";
      isSearching = false;
    }
  }

  // ====== Submit filter ======
  filterForm?.addEventListener("submit", function (e) {
    e.preventDefault();
    updateTags();
    sendAjaxFilter(1);
    const newUrl = new URL(window.location.href);
    newUrl.searchParams.delete("page");
    window.history.pushState({}, "", newUrl);
  });

  // ====== Xoá tag ======
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
    sendAjaxFilter(1);
  });

  // ====== Clear All ======
  clearAllBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    document
      .querySelectorAll("#filter-form input[type='checkbox']")
      .forEach((cb) => (cb.checked = false));
    const sel = document.querySelector("select[name='price_range']");
    if (sel) sel.value = "";
    tagContainer.querySelectorAll(".tag").forEach((t) => t.remove());
    sendAjaxFilter(1);
  });

  // ====== Pagination ======
  function setupPagination() {
    const paginationLinks = document.querySelectorAll(".pagination-link");
    paginationLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        const page = this.dataset.page;
        if (page) {
          sendAjaxFilter(parseInt(page));
          const shopContainer = document.querySelector(".shop-container");
          if (shopContainer)
            shopContainer.scrollIntoView({ behavior: "smooth" });
        }
      });
    });
  }

  // ====== Load lại khi có keyword ======
  const urlParams = new URLSearchParams(window.location.search);
  const initialKeyword = urlParams.get("keyword");
  if (initialKeyword) {
    searchInput.value = decodeURIComponent(initialKeyword);
    setTimeout(() => sendAjaxFilter(1), 250);
  }

  // ====== Lần đầu load ======
  updateTags();
  setupPagination();
  window.addEventListener("load", () => {
    loadingSpinner.style.display = "none";
    productGrid.style.opacity = "1";
    paginationContainer.style.opacity = "1";
  });
});
