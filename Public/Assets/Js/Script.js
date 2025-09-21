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

const SEASON_ASSETS_LOGO = {
  spring: { logo: BASE_URL + "Seasons/Logo/Xuan.png" },
  summer: { logo: BASE_URL + "Seasons/Logo/Ha.png" },
  autumn: { logo: BASE_URL + "Seasons/Logo/Thu.png" },
  winter: { logo: BASE_URL + "Seasons/Logo/Dong.png" },
};

const SEASON_ASSETS_BANNER_LEFT = {
  spring: { banner: BASE_URL + "Seasons/Banner_Left/Spring.png" },
  summer: { banner: BASE_URL + "Seasons/Banner_Left/Summer.png" },
  autumn: { banner: BASE_URL + "Seasons/Banner_Left/Autumn.png" },
  winter: { banner: BASE_URL + "Seasons/Banner_Left/Winter.png" },
};

const SEASON_ASSETS_BANNER_RIGHT_1 = {
  spring: {
    banner: BASE_URL + "Assets/Image/Main/Section_1/Banner_Spring_1.jpeg",
  },
  summer: {
    banner: BASE_URL + "Assets/Image/Main/Section_1/Banner_Summer_1.jpg",
  },
  autumn: {
    banner: BASE_URL + "Assets/Image/Main/Section_1/Banner_Autumn_1.jpeg",
  },
  winter: {
    banner: BASE_URL + "Assets/Image/Main/Section_1/Banner_Winter_1.jpeg",
  },
};

const SEASON_ASSETS_BANNER_RIGHT_2 = {
  spring: {
    banner: BASE_URL + "Assets/Image/Main/Section_1/Banner_Spring_2.jpeg",
  },
  summer: {
    banner: BASE_URL + "Assets/Image/Main/Section_1/Banner_Summer_2.jpeg",
  },
  autumn: {
    banner: BASE_URL + "Assets/Image/Main/Section_1/Banner_Autumn_2.jpg",
  },
  winter: {
    banner: BASE_URL + "Assets/Image/Main/Section_1/Banner_Winter_2.jpeg",
  },
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
  const banner_Left = document.querySelector("img.hero-decor");
  const banner_right = document.querySelectorAll(".hero-image img");
  const banner_right_1 = banner_right[0];
  const banner_right_2 = banner_right[1];

  if (
    logoImg &&
    SEASON_ASSETS_LOGO[season]?.logo &&
    SEASON_ASSETS_BANNER_LEFT[season]?.banner &&
    SEASON_ASSETS_BANNER_RIGHT_1[season]?.banner &&
    SEASON_ASSETS_BANNER_RIGHT_2[season]?.banner
  ) {
    logoImg.src = SEASON_ASSETS_LOGO[season].logo;
    logoImgFooter.src = SEASON_ASSETS_LOGO[season].logo;
    logoImg.alt = season;
    logoImgFooter.alt = season;

    banner_Left.src = SEASON_ASSETS_BANNER_LEFT[season].banner;
    banner_Left.alt = season;

    banner_right_1.src = SEASON_ASSETS_BANNER_RIGHT_1[season].banner;
    banner_right_1.alt = season;

    banner_right_2.src = SEASON_ASSETS_BANNER_RIGHT_2[season].banner;
    banner_right_2.alt = season;
  }
}

/** Đọc ?season=summer từ URL*/
function getSeasonFromQuery() {
  const params = new URLSearchParams(window.location.search);
  const s = params.get("season");
  return s ? s.toLowerCase() : null;
}

/** Khởi tạo mùa:
 *  - Ưu tiên: query (?season=/Web_Hoa.) > tự động theo tháng
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
document.querySelectorAll(".favorite").forEach((btn) => {
  btn.addEventListener("click", function () {
    const icon = this.querySelector("i");
    icon.classList.toggle("fa-regular");
    icon.classList.toggle("fa-solid");
  });
});
