<?php
  include_once __DIR__ . '/../../Includes/config.php';

  // Head
  include_once __DIR__ . '/../../Includes/head.php';
  include_once __DIR__ . '/../Layouts/Header.php';
?>

<div class="checkout-payment">
  <!-- BÊN TRÁI: DANH SÁCH PHƯƠNG THỨC THANH TOÁN -->
  <div class="checkout-payment__left">
    <h2 class="checkout-payment__title">Thêm Tài Khoản Thanh Toán</h2>
    <div class="payment-methods-list">
      <label class="payment-method">
        <input type="radio" name="payment" checked>
        <span class="payment-method__icon"><img src="paypal.png" alt="Paypal" /></span>
        <span class="payment-method__label">Paypal</span>
      </label>

      <label class="payment-method">
        <input type="radio" name="payment">
        <span class="payment-method__icon"><img src="visa.png" alt="Visa" /></span>
        <span class="payment-method__label">VISA</span>
      </label>

      <label class="payment-method">
        <input type="radio" name="payment">
        <span class="payment-method__icon"><img src="applepay.png" alt="MoMo" /></span>
        <span class="payment-method__label">MoMo</span>
      </label>

      <label class="payment-method">
        <input type="radio" name="payment">
        <span class="payment-method__icon"><img src="cod.png" alt="COD" /></span>
        <span class="payment-method__label">Thanh toán khi nhận hàng (Tiền Mặt)</span>
      </label>
    </div>
    <div class="add-new-card">
      <label class="add-new-card__radio">
        <input type="radio" name="payment">
        <span class="add-new-card__label">Thêm Thẻ Tín Dụng / Ghi Nợ Mới</span>
      </label>

      <div class="add-new-card__form">
        <input type="text" placeholder="Tên Chủ Thẻ*" required />
        <input type="text" placeholder="Số Thẻ*" required />
        <div class="add-new-card__row">
          <input type="text" placeholder="Ngày Hết Hạn*" required />
          <input type="text" placeholder="CVV*" required />
        </div>
        <label class="add-new-card__save">
          <input type="checkbox" />
          Lưu thẻ cho các lần thanh toán sau
        </label>
        <button class="add-new-card__button">Thêm Thẻ</button>
      </div>
    </div>
  </div>


</div>

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>

<!-- Script -->
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
