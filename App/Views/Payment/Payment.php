<?php
  include_once __DIR__ . '/../../Includes/config.php';

  // Head
  include_once __DIR__ . '/../../Includes/head.php';
  include_once __DIR__ . '/../Layouts/Header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/User/Payment.css?v=<?= time() ?>">

<div class="checkout-payment">
  <div class="checkout-payment__left">
    <h2 class="checkout-payment__title">Thêm Tài Khoản Thanh Toán</h2>

    <form class="add-new-card__form"
          method="POST"
          action="<?= BASE_URL ?>index.php?controller=auth&action=handleAddNewCard">

      <!-- 🔹 DANH SÁCH PHƯƠNG THỨC -->
      <div class="payment-methods-list">
        <label class="payment-method">
          <input type="radio" name="card_brand" value="PayPal" required>
          <span class="payment-method__icon">
            <img src="<?= BASE_URL ?>Public/Assets/Image/logo_payment/paypal.png" alt="PayPal">
          </span>
          <span class="payment-method__label">PayPal</span>
        </label>

        <label class="payment-method">
          <input type="radio" name="card_brand" value="Visa">
          <span class="payment-method__icon">
            <img src="<?= BASE_URL ?>Public/Assets/Image/logo_payment/visa.png" alt="Visa">
          </span>
          <span class="payment-method__label">Visa / MasterCard</span>
        </label>

        <label class="payment-method">
          <input type="radio" name="card_brand" value="MoMo">
          <span class="payment-method__icon">
            <img src="<?= BASE_URL ?>Public/Assets/Image/logo_payment/momo.png" alt="MoMo">
          </span>
          <span class="payment-method__label">MoMo</span>
        </label>

        <label class="payment-method">
          <input type="radio" name="card_brand" value="COD">
          <span class="payment-method__icon">
            <img src="<?= BASE_URL ?>Public/Assets/Image/logo_payment/cod.png" alt="COD">
          </span>
          <span class="payment-method__label">Thanh Toán Khi Nhận Hàng (COD)</span>
        </label>
      </div>

      <!-- 🔹 NHẬP THÔNG TIN THẺ -->
      <input type="text" name="card_holder" placeholder="Tên Chủ Thẻ*" required>
      <input type="text" name="card_number" placeholder="Số Thẻ*" required>

      <div class="add-new-card__row">
        <input type="text" name="expiry_date" placeholder="Ngày Hết Hạn (MM/YY)*" required>
        <input type="text" name="cvv" placeholder="CVV*" required>
      </div>

      <label class="add-new-card__save">
        <input type="checkbox" name="save_card" checked> Lưu thẻ cho các lần thanh toán sau
      </label>

      <button type="submit" class="add-new-card__button">Thêm Thẻ</button>
    </form>
  </div>
</div>


<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>

<!-- Script -->
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
