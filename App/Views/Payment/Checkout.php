<?php
  include_once __DIR__ . '/../../Includes/config.php';

  // Head
  include_once __DIR__ . '/../../Includes/head.php';
  include_once '../Layouts/Header.php';
?>

<div class="checkout-container">
  <div class="checkout-left">
    <h2 class="section-title">Thông Tin Thanh Toán</h2>
    <form class="billing-form">
      <div class="form-group-row">
        <div class="form-group">
          <label>Họ*</label>
          <input type="text" name="first_name" required>
        </div>
        <div class="form-group">
          <label>Tên*</label>
          <input type="text" name="last_name" required>
        </div>
      </div>

      <div class="form-group">
        <label>Quốc Gia*</label>
        <select name="country" required>
          <option value="">Chọn quốc gia</option>
          <option value="Vietnam">Việt Nam</option>
          <option value="USA">Mỹ</option>
        </select>
      </div>

      <div class="form-group">
        <label>Địa Chỉ*</label>
        <input type="text" name="street" placeholder="Số nhà, tên đường" required>
      </div>

      <div class="form-group-row">
        <div class="form-group">
          <label>Thành Phố*</label>
          <select name="city" required>
            <option value="">Chọn thành phố</option>
            <option value="Hanoi">Hà Nội</option>
            <option value="HCM">Hồ Chí Minh</option>
          </select>
        </div>
        <div class="form-group">
          <label>Tỉnh / Quận*</label>
          <select name="state" required>
            <option value="">Chọn tỉnh / quận</option>
            <option value="State A">Khu vực A</option>
            <option value="State B">Khu vực B</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Mã Bưu Điện (không bắt buộc)</label>
        <input type="text" name="zip">
      </div>

      <div class="form-group">
        <label>Số Điện Thoại*</label>
        <input type="tel" name="phone" required>
      </div>

      <div class="form-group">
        <label>Email*</label>
        <input type="email" name="email" required>
      </div>

      <div class="form-group">
        <label>Địa Chỉ Giao Hàng*</label>
        <div class="radio-group">
          <label><input type="radio" name="delivery" checked> Giống địa chỉ thanh toán</label>
          <label><input type="radio" name="delivery"> Sử dụng địa chỉ khác</label>
        </div>
      </div>
    </form>
  </div>

  <div class="checkout-right">
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
