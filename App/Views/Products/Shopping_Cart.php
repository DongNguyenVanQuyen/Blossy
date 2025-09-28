<?php
  include_once __DIR__ . '/../../Includes/config.php';

  // Head
  include_once __DIR__ . '/../../Includes/head.php';
  include_once '../Layouts/Header.php';
?>

<!-- shopping_cart.php -->
<div class="cart-container">
  <div class="cart-left">
    <h2 class="cart-title">Giỏ Hàng</h2>
    <div class="cart-list">
      <div class="cart-item">
        <img src="ảnh_hoa.jpg" alt="Elegant Touch" />
        <div class="cart-info">
          <div class="category">Hoa Bó</div>
          <div class="name">Elegant Touch</div>
          <div class="cart-quantity">
            <button>-</button>
            <span>3</span>
            <button>+</button>
          </div>
        </div>
        <div class="cart-price">750.000đ</div>
        <div class="cart-subtotal">2.250.000đ</div>
        <div class="cart-remove">×</div>
      </div>
    </div>

    <!-- Footer: Mã giảm giá -->
    <div class="cart-footer">
      <input type="text" placeholder="Mã giảm giá">
      <button class="apply-btn">Áp Dụng</button>
      <div class="clear-cart">Xóa Giỏ Hàng</div>
    </div>
  </div>

  <div class="cart-right">
    <h3>Tóm Tắt Đơn Hàng</h3>
    <div class="summary-item">Sản phẩm <span>9</span></div>
    <div class="summary-item">Tạm tính <span>9.360.000đ</span></div>
    <div class="summary-item">Phí vận chuyển <span>Miễn phí</span></div>
    <div class="summary-item">Giảm giá <span>-960.000đ</span></div>
    <div class="summary-item total">Tổng cộng <span>8.400.000đ</span></div>
    <button class="checkout-btn">Thanh Toán</button>
  </div>
</div>

<?php include_once '../Layouts/Footer.php'; ?>

<!-- Script -->
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
