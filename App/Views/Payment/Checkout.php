<?php
include_once __DIR__ . '/../../Includes/config.php';
include_once __DIR__ . '/../../Includes/head.php';
include_once __DIR__ . '/../Layouts/Header.php';
?>

<div class="checkout-container">
  <form id="checkout-form" method="POST" action="index.php?controller=order&action=complete">

    <!-- ==================== LEFT ==================== -->
    <div class="checkout-left">
      <h2 class="section-title">Thông Tin Thanh Toán</h2>

      <div id="billing-form" class="billing-form">
        <div class="form-group-row">
          <div class="form-group">
            <label>Họ*</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>Tên*</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
          </div>
        </div>
        <?php
        $defaultAddress = $user_address[0] ?? null; // lấy địa chỉ đầu tiên (đã ORDER BY is_default DESC)
        ?>
        <div class="form-group">
          <label>Địa Chỉ*</label>
          <?php
          $addressValue = $defaultAddress['line1'] ?? $user['address'] ?? '';
          ?>
          <input type="text" name="street" placeholder="Số nhà, tên đường"
                value="<?= htmlspecialchars($addressValue) ?>" required>

        </div>


        <div class="form-group">
          <label>Thành Phố*</label>
          <input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label>Số Điện Thoại*</label>
          <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label>Email*</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>

        <!-- ✅ Radio địa chỉ giao hàng -->
        <div class="form-group delivery-choice">
          <label>Địa Chỉ Giao Hàng*</label>
          <div class="radio-wrapper">
            <label class="radio-item">
              <input type="radio" name="delivery" value="default" checked>
              <span>Địa chỉ mặc định</span>
            </label>
            <label class="radio-item">
              <input type="radio" name="delivery" value="new">
              <span>Sử dụng địa chỉ mới</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- ==================== RIGHT ==================== -->
    <div class="checkout-right">
      <h3>Tóm Tắt Đơn Hàng</h3>

      <!-- 🔹 DANH SÁCH SẢN PHẨM -->
      <div class="checkout-products">
        <?php foreach ($cartItems as $item): ?>
          <div class="checkout-product">
            <div class="checkout-product-info">
              <p class="checkout-product-name"><?= htmlspecialchars($item['name']) ?></p>
              <span><?= htmlspecialchars($item['quantity']) ?> × <?= number_format($item['price'], 0, ',', '.') ?>đ</span>
            </div>
            <strong><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</strong>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- 🔹 ÁP DỤNG MÃ VOUCHER -->
     <div class="voucher-section">
      <h4>Áp dụng mã giảm giá</h4>

    <div class="voucher-form">
      <input type="text"
            id="voucher-input"
            placeholder="Nhập mã voucher..."
            class="voucher-input">
      <button type="button"
              class="voucher-apply-btn"
              id="apply-voucher">Áp dụng</button>
    <input type="hidden" name="voucher_code" id="voucher_code" value="">
    <input type="hidden" name="voucher_discount" id="voucher_discount" value="0">

    </div>


    <p id="voucher-message"></p>


      <?php if (isset($voucher) && $voucher): ?>
        <p class="voucher-success">
          ✅ Mã <strong><?= htmlspecialchars($voucher['code']) ?></strong> 
          đã được áp dụng 
          <?php if ($voucher['type'] === 'percent'): ?>
            (Giảm <?= $voucher['value'] ?>%)
          <?php else: ?>
            (Giảm <?= number_format($voucher['value'], 0, ',', '.') ?>đ)
          <?php endif; ?>
        </p>
      <?php elseif (!empty($_GET['voucher'])): ?>
        <p class="voucher-error">❌ Mã không hợp lệ hoặc không còn hiệu lực.</p>
      <?php endif; ?>
    </div>



      <!-- 🔹 TÓM TẮT TIỀN -->
    <div class="summary-item summary-count">Sản phẩm <span><?= $totals['count'] ?></span></div>
    <div class="summary-item summary-subtotal">Tạm tính <span><?= $totals['subtotal'] ?></span></div>
    <div class="summary-item summary-discount">Giảm giá <span>-<?= $totals['discount'] ?></span></div>
    <div class="summary-item summary-shipping">Vận chuyển <span>30.000đ</span></div>
    <div class="summary-item summary-total total">Tổng cộng <span><?= $totals['total'] ?></span></div>


      <!-- 🔹 PHƯƠNG THỨC THANH TOÁN -->
      <div class="payment-method-section">
        <h4>Chọn Phương Thức Thanh Toán</h4>
        <div class="payment-methods">
          <label class="payment-method payment-active <?= in_array('Visa', $methods) ? '' : 'disabled' ?>">
            <input type="radio" name="payment_method" value="visa" <?= in_array('Visa', $methods) ? '' : 'disabled' ?>>
            <img src="https://cdn-icons-png.flaticon.com/512/349/349221.png" alt="Visa" />
            <span>Thẻ Visa / MasterCard</span>
          </label>

          <label class="payment-method payment-active <?= in_array('PayPal', $methods) ? '' : 'disabled' ?>">
            <input type="radio" name="payment_method" value="paypal" <?= in_array('PayPal', $methods) ? '' : 'disabled' ?>>
            <img src="https://cdn-icons-png.flaticon.com/512/196/196565.png" alt="PayPal" />
            <span>PayPal</span>
          </label>

          <label class="payment-method payment-active <?= in_array('MoMo', $methods) ? '' : 'disabled' ?>">
            <input type="radio" name="payment_method" value="momo" <?= in_array('MoMo', $methods) ? '' : 'disabled' ?>>
            <img src="<?= BASE_URL ?>Public/Assets/Image/logo_payment/momo.png" alt="MoMo" />
            <span>MoMo</span>
          </label>

          <label class="payment-method">
            <input type="radio" name="payment_method" value="cod" checked>
            <img src="https://cdn-icons-png.flaticon.com/512/709/709790.png" alt="COD" />
            <span>Thanh Toán Khi Nhận Hàng (COD)</span>
          </label>
        </div>
      </div>

      <!-- 🔹 NÚT SUBMIT -->
      <button type="submit" class="checkout-btn">Thanh Toán</button>
    </div>
  </form>
</div>

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
<script src="<?= BASE_URL ?>Public/Assets/Js/Checkout.js?v=<?= time() ?>"></script>
