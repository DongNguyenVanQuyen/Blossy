document.addEventListener("DOMContentLoaded", () => {
  // Rating chọn sao
  const stars = document.querySelectorAll("#starRating .fa-star");
  const ratingInput = document.getElementById("ratingInput");

  stars.forEach((star) => {
    star.addEventListener("click", () => {
      const value = parseInt(star.dataset.value);
      ratingInput.value = value;
      stars.forEach((s) => {
        s.classList.toggle("active", parseInt(s.dataset.value) <= value);
      });
    });
  });

  // Xem trước ảnh upload
  const fileInput = document.getElementById("reviewImages");
  const previewArea = document.getElementById("previewArea");

  fileInput.addEventListener("change", () => {
    previewArea.innerHTML = "";
    const files = Array.from(fileInput.files).slice(0, 5);
    files.forEach((file) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const img = document.createElement("img");
        img.src = e.target.result;
        previewArea.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  });
});
