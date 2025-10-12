function previewImage(input, targetId) {
  const preview = document.getElementById(targetId);
  preview.innerHTML = "";
  if (input.files && input.files[0]) {
    const img = document.createElement("img");
    img.src = URL.createObjectURL(input.files[0]);
    preview.appendChild(img);
  }
}

function previewMulti(input, targetId) {
  const container = document.getElementById(targetId);
  container.innerHTML = "";
  if (input.files) {
    Array.from(input.files).forEach((file) => {
      const img = document.createElement("img");
      img.src = URL.createObjectURL(file);
      container.appendChild(img);
    });
  }
}
document.addEventListener("DOMContentLoaded", () => {
  const mainBtn = document.getElementById("upload-main-btn");
  const mainFile = document.getElementById("main_image_file");
  const mainPreview = document.getElementById("preview-main");
  const mainUrlInput = document.getElementById("main_url");

  const subBtn = document.getElementById("upload-sub-btn");
  const subFiles = document.getElementById("sub_image_files");
  const subPreview = document.getElementById("preview-gallery");
  const subContainer = document.getElementById("sub_urls_container");

  // ==== Upload ảnh chính ====
  mainBtn.addEventListener("click", async () => {
    const file = mainFile.files[0];
    if (!file) return alert("⚠️ Hãy chọn một ảnh trước!");

    const formData = new FormData();
    formData.append("file", file);

    mainBtn.disabled = true;
    mainBtn.textContent = "⏳ Đang tải...";

    try {
      const res = await fetch("index.php?controller=upload&action=main", {
        method: "POST",
        body: formData,
      });
      const data = await res.json();

      if (data.success) {
        mainUrlInput.value = data.url;
        mainPreview.innerHTML = `<img src="${data.url}" alt="Ảnh chính mới">`;
        alert("✅ Ảnh chính đã tải lên!");
      } else {
        alert("❌ Upload thất bại!");
      }
    } catch (err) {
      console.error("Lỗi upload:", err);
      alert("❌ Lỗi khi upload!");
    } finally {
      mainBtn.disabled = false;
      mainBtn.textContent = "📤 Tải lên";
    }
  });

  // ==== Upload ảnh phụ ====
  subBtn.addEventListener("click", async () => {
    const files = Array.from(subFiles.files);
    if (!files.length) return alert("⚠️ Hãy chọn ít nhất 1 ảnh!");

    subBtn.disabled = true;
    subBtn.textContent = "⏳ Đang tải...";

    subPreview.innerHTML = "";
    subContainer.innerHTML = "";

    for (const file of files) {
      const formData = new FormData();
      formData.append("file", file);

      try {
        const res = await fetch("index.php?controller=upload&action=main", {
          method: "POST",
          body: formData,
        });
        const data = await res.json();

        if (data.success) {
          subPreview.innerHTML += `<img src="${data.url}" alt="Ảnh phụ">`;
          subContainer.innerHTML += `<input type="hidden" name="sub_urls[]" value="${data.url}">`;
        } else {
          alert("❌ Upload thất bại cho 1 ảnh!");
        }
      } catch (err) {
        console.error("Lỗi upload:", err);
      }
    }

    alert("✅ Tất cả ảnh phụ đã tải lên!");
    subBtn.disabled = false;
    subBtn.textContent = "📤 Tải lên";
  });
});
