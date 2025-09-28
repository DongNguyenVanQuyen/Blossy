<?php include_once __DIR__ . '/../../Includes/config.php'; ?>
<?php include_once __DIR__ . '/../../Includes/head.php'; ?>
<?php include_once  __DIR__ . '/../Layouts/Header.php'; ?>

<section class="contact-section">
  <header class="shop-header">
    <h1>Liên Hệ</h1>
    <p><a href="#">Trang chủ</a> / Liên hệ</p>
  </header>

  <div class="contact-wrapper">
    <!-- Ảnh liên hệ -->
    <div class="contact-image">
      <img src="<?= BASE_URL ?>Public/Assets/Image/contact-girl.jpg" alt="Liên hệ">
    </div>

    <!-- Form liên hệ -->
    <form class="contact-form" method="POST" action="#">
      <div class="row">
        <div class="form-group">
          <label>Họ *</label>
          <input type="text" name="first_name" placeholder="Nhập họ">
        </div>
        <div class="form-group">
          <label>Tên *</label>
          <input type="text" name="last_name" placeholder="Nhập tên">
        </div>
      </div>

      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" placeholder="Nhập địa chỉ email">
      </div>

      <div class="form-group">
        <label>Số điện thoại *</label>
        <input type="tel" name="phone" placeholder="Nhập số điện thoại">
      </div>

      <div class="form-group">
        <label>Tiêu đề *</label>
        <input type="text" name="subject" placeholder="Nhập tiêu đề">
      </div>

      <div class="form-group">
        <label>Nội dung *</label>
        <textarea name="message" rows="5" placeholder="Viết lời nhắn tại đây..."></textarea>
      </div>

      <button type="submit" class="btn-send">Gửi liên hệ</button>
    </form>
  </div>

  <!-- Thông tin liên hệ -->
  <div class="contact-info">
    <div class="info-box">
      <img src="<?= BASE_URL ?>Public/Assets/Icon/email.png" alt="email icon">
      <h4>Email</h4>
      <p>Support@flowry.com</p>
    </div>
    <div class="info-box">
      <img src="<?= BASE_URL ?>Public/Assets/Icon/phone.png" alt="phone icon">
      <h4>Điện thoại</h4>
      <p>+01 0387 29475</p>
    </div>
    <div class="info-box">
      <img src="<?= BASE_URL ?>Public/Assets/Icon/location.png" alt="location icon">
      <h4>Địa chỉ</h4>
      <p>123 Bloom Street, Dokki, NY</p>
    </div>
  </div>
</section>

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
