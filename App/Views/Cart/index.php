<?php
  include_once __DIR__ . '/../../Includes/config.php';
  include_once __DIR__ . '/../../Includes/head.php';
  include_once __DIR__ . '/../Layouts/Header.php';
?>

<!-- shopping_cart.php -->
<div class="cart-container">
  <div class="cart-left">
    <h2 class="cart-title">Giỏ Hàng</h2>

    <div class="cart-list">
      <?php if (empty($cart)): ?>
        <p class="empty-cart">🛒 Giỏ hàng của bạn đang trống.</p>
      <?php else: ?>
        <?php foreach ($cart as $item): ?>
          <div class="cart-item" data-id="<?= htmlspecialchars($item['id'] ?? $item['product_id'] ?? '') ?>">
             <img src="<?= htmlspecialchars($item['image_url'] ?? $item['image'] ?? '') ?>"
                 alt="<?= htmlspecialchars($item['name'] ?? '') ?>">
            <div class="cart-info">
              <div class="category">Hoa Bó</div>
              <div class="name"><?= htmlspecialchars($item['name']) ?></div>
             <div class="cart-quantity">
                <button class="qty-btn minus">-</button>
                <span><?= $item['quantity'] ?></span>
                <button class="qty-btn plus">+</button>
              </div>
            </div>
            <div class="cart-price-old"><?= number_format($item['price_old'], 0, ',', '.') ?>đ</div>
            <div class="cart-price"><?= number_format($item['price'], 0, ',', '.') ?>đ</div>
            <div class="cart-subtotal">
              <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ
            </div>  
            <div class="cart-remove">×</div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
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

  <?php
    // Nếu giỏ hàng trống thì gán 0
    if (empty($cart)) {
      $totalItems = 0;
      $subtotal = 0;
    } else {
      $totalItems = array_sum(array_column($cart, 'quantity'));
      $subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
    }
  ?>

  <div class="summary-item">Sản phẩm <span><?= $totalItems ?></span></div>
  <div class="summary-item">Tạm tính <span><?= number_format($subtotal, 0, ',', '.') ?>đ</span></div>
  <div class="summary-item">Phí vận chuyển <span>Miễn phí</span></div>
  <div class="summary-item">Giảm giá <span>-0đ</span></div>
  <div class="summary-item total">Tổng cộng <span><?= number_format($subtotal, 0, ',', '.') ?>đ</span></div>

<button 
  class="checkout-btn checkout-now-card" 
  data-id="<?= htmlspecialchars($item['id'] ?? $item['product_id'] ?? '') ?>" 
  <?= $totalItems === 0 ? 'disabled' : '' ?>>
  Thanh Toán
</button>

</div>

</div>

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
<script src="<?= BASE_URL ?>Public/Assets/Js/Cart.js?v=<?= time() ?>"></script>
