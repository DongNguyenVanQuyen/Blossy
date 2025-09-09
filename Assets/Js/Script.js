function updateTime() {
  const timeEl = document.getElementById("Time");
  const now = new Date();

  const options = {
    weekday: "short", // Thứ
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  };

  // Lấy mùa
  const root = document.documentElement;
  const currentSeason = ["spring", "summer", "autumn", "winter"].find((s) =>
    root.classList.contains(s)
  );

  // Chọn icon tương ứng - mùa
  const seasonIcons = {
    spring: "🌸",
    summer: "☀️",
    autumn: "🍂",
    winter: "❄️",
  };

  const icon = seasonIcons[currentSeason] || "";

  // Gán text vào
  timeEl.textContent = `${icon} ${now.toLocaleString("vi-VN", options)}`;
}

window.addEventListener("DOMContentLoaded", function () {
  updateTime();
  setInterval(updateTime, 1000);
});

const SEASON_ASSETS = {
  spring: { logo: "../Seasons/Logo/Xuan.png" },
  summer: { logo: "../Seasons/Logo/Ha.png" },
  autumn: { logo: "../Seasons/Logo/Thu.png" },
  winter: { logo: "../Seasons/Logo/Dong.png" },
};

/*  Xuân: 2-4, Hạ: 5-7, Thu: 8-10, Đông: 11-1 */
function detectSeasonByMonth(date = new Date()) {
  const m = date.getMonth() + 1; // 1-12
  if (m === 11 || m === 12 || m === 1) return "winter";
  if (m >= 2 && m <= 4) return "spring";
  if (m >= 5 && m <= 7) return "summer";
  return "autumn"; // 8-10
}

/** Set Mùa*/
function setSeason(season) {
  const seasons = ["spring", "summer", "autumn", "winter"];
  if (!seasons.includes(season)) season = "spring";

  // Xóa class mùa cũ, thêm class mới vào <html>
  const root = document.documentElement;
  seasons.forEach((s) => root.classList.remove(s));
  root.classList.add(season);

  // Đổi logo
  const logoImg = document.querySelector("#Logo img");
  const logoImgFooter = document.querySelector(".footer-logo img");
  if (logoImg && logoImgFooter && SEASON_ASSETS[season]?.logo) {
    logoImg.src = SEASON_ASSETS[season].logo;
    logoImgFooter.src = SEASON_ASSETS[season].logo;
    logoImg.alt = season;
    logoImgFooter.alt = season;
  }
}

/** Đọc ?season=summer từ URL*/
function getSeasonFromQuery() {
  const params = new URLSearchParams(window.location.search);
  const s = params.get("season");
  return s ? s.toLowerCase() : null;
}

/** Khởi tạo mùa:
 *  - Ưu tiên: query (?season=...) > localStorage > tự động theo tháng
 */
(function initSeason() {
  const fromQuery = getSeasonFromQuery(); // lay tu URL
  const autoDetected = detectSeasonByMonth(); // lay tu thang

  // uu tien
  window.addEventListener("DOMContentLoaded", () => {
    setSeason(fromQuery || autoDetected);
  });
  //setSeason("spring");
  //setSeason("summer");
  //setSeason("autumn");
  //setSeason("winter");
})();
