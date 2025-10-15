<?php include_once __DIR__ . '/../../Includes/config.php'; ?>

<!-- Head -->
<?php include_once __DIR__ . '/../../Includes/head.php'; ?>

<!-- Header -->
<?php include_once __DIR__ . '/../Layouts/Header.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Pages/About.css?v-<?= time() ?>">
<section class="about-section">
    <header class="shop-header">
      <h1>Giới Thiệu</h1>
      <p><a href="#">Trang chủ</a> / Giới Thiệu</p>
    </header>
  <div class="about-wrapper">
    <!-- Hình tròn -->
    <div class="about-image">
      <img src="Public/Assets/Image/about-flowers.jpg" alt="Giới thiệu">
    </div>

    <!-- Nội dung giới thiệu -->
    <div class="about-content">
      <h2>Về Chúng Tôi</h2>
      <p>
        Tại <span class="brand">Blossy</span>, chúng tôi tạo ra những bó hoa tươi, tinh tế mang lại niềm vui cho mọi dịp đặc biệt.
        <br>
        Với tình yêu cái đẹp và sự chăm chút trong từng chi tiết, chúng tôi giúp việc lan tỏa hạnh phúc trở nên thật dễ dàng qua từng đóa hoa.
      </p>

      <div class="about-stats">
        <div class="stat-box">
          <h3>30+</h3>
          <p>Loại sản phẩm</p>
        </div>
        <div class="stat-box">
          <h3>99%</h3>
          <p>Khách hàng hài lòng</p>
        </div>
        <div class="stat-box">
          <h3>900+</h3>
          <p>Sản phẩm</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Sứ mệnh -->
  <div class="mission-box">
    <h2>Sứ Mệnh Của Chúng Tôi</h2>
    <p>
      Biến mỗi ngày trở nên tươi đẹp hơn bằng cách trao gửi những bó hoa tươi, được sắp xếp tinh tế để lan tỏa niềm vui, tôn vinh khoảnh khắc và kết nối trái tim.
    </p>
    <a href="<?= BASE_URL ?>index.php?controller=products&action=index" class="btn-mission">Mua Ngay</a>
  </div>

  <!-- Hoa nền -->
  <img src="Public/Assets/Image/flower-decor.png" alt="Trang trí hoa" class="flower-bg" />
</section>

<!-- Footer -->
<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>

<!-- Script -->
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
