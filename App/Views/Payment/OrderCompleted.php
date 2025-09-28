<?php
  include_once __DIR__ . '/../../Includes/config.php';

  // Head
  include_once __DIR__ . '/../../Includes/head.php';
  include_once '../Layouts/Header.php';
?>

<div class="order-completed">
  <div class="order-completed__header">
    <h1 class="order-completed__title">Hoàn Tất Đơn Hàng</h1>
    <p class="order-completed__breadcrumb">Trang chủ / Hoàn Tất Đơn Hàng</p>
  </div>

  <div class="order-completed__status">
    <div class="order-completed__icon">✅</div>
    <h2 class="order-completed__message">Đơn hàng của bạn đã được xử lý thành công!</h2>
    <p class="order-completed__note">Cảm ơn bạn. Đơn hàng của bạn đã được ghi nhận.</p>
  </div>

  <div class="order-completed__summary">
    <div class="order-completed__summary-item">
      <strong>Mã Đơn Hàng</strong>
      <span>#DIUEND738</span>
    </div>
    <div class="order-completed__summary-item">
      <strong>Phương Thức Thanh Toán</strong>
      <span>Visa</span>
    </div>
    <div class="order-completed__summary-item">
      <strong>Mã Giao Dịch</strong>
      <span>TR382SPFE</span>
    </div>
    <div class="order-completed__summary-item">
      <strong>Ngày Giao Dự Kiến</strong>
      <span>19 Tháng 01, 2025</span>
    </div>
    <div class="order-completed__summary-item">
      <button class="order-completed__summary-button">Tải Hóa Đơn</button>
    </div>
  </div>

  <div class="order-completed__details">
    <h3>Chi Tiết Đơn Hàng</h3>

    <div class="order-completed__product-list">
      <div class="order-completed__product-item">
        <div class="order-completed__product-left">
          <img src="path/to/image1.jpg" alt="Elegant Touch">
          <div class="order-completed__product-info">
            <small>Hoa Bó</small>
            <p>Elegant Touch</p>
            <span class="order-completed__product-qty">Số lượng: 1</span>
          </div>
        </div>
        <div class="order-completed__product-price">2.300.000đ</div>
      </div>

      <div class="order-completed__product-item">
        <div class="order-completed__product-left">
          <img src="path/to/image2.jpg" alt="With All My Heart">
          <div class="order-completed__product-info">
            <small>Hoa Bó</small>
            <p>With All My Heart</p>
            <span class="order-completed__product-qty">Số lượng: 2</span>
          </div>
        </div>
        <div class="order-completed__product-price">1.400.000đ</div>
      </div>

      <div class="order-completed__product-item">
        <div class="order-completed__product-left">
          <img src="path/to/image3.jpg" alt="Heart's Whisper">
          <div class="order-completed__product-info">
            <small>Hoa Bó</small>
            <p>Heart's Whisper</p>
            <span class="order-completed__product-qty">Số lượng: 1</span>
          </div>
        </div>
        <div class="order-completed__product-price">2.300.000đ</div>
      </div>

      <div class="order-completed__product-item">
        <div class="order-completed__product-left">
          <img src="path/to/image4.jpg" alt="Birthday Bliss">
          <div class="order-completed__product-info">
            <small>Hoa Bó</small>
            <p>Birthday Bliss</p>
            <span class="order-completed__product-qty">Số lượng: 1</span>
          </div>
        </div>
        <div class="order-completed__product-price">3.400.000đ</div>
      </div>
    </div>

    <div class="order-completed__summary-total">
      <span>Phí Vận Chuyển</span>
      <span>Miễn phí</span>
    </div>
    <div class="order-completed__summary-total">
      <span>Giảm Giá</span>
      <span>-960.000đ</span>
    </div>
    <div class="order-completed__summary-total">
      <strong>Tổng Cộng</strong>
      <strong>8.400.000đ</strong>
    </div>
  </div>
</div>

<?php include_once '../Layouts/Footer.php'; ?>

<!-- Script -->
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
