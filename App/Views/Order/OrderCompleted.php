  <?php
  include_once __DIR__ . '/../../Includes/config.php';

  // Head
  include_once __DIR__ . '/../../Includes/head.php';
  include_once __DIR__ . '/../Layouts/Header.php';

  // ✅ Đảm bảo biến có dữ liệu, tránh lỗi Notice nếu trống
  $order = $order ?? [];
  $items = $items ?? [];
  ?>

  <div class="order-completed">
    <!-- 🔹 TRẠNG THÁI ĐƠN HÀNG -->
    <div class="order-completed__status">
      <div class="order-completed__icon">✅</div>
      <h2>Đơn hàng của bạn đã được xử lý thành công!</h2>
      <p>Cảm ơn bạn, đơn hàng của bạn đã được ghi nhận.</p>
    </div>

    <!-- 🔹 TÓM TẮT ĐƠN HÀNG -->
    <div class="order-completed__summary">
      <div><strong>Mã Đơn Hàng:</strong> <span>#<?= htmlspecialchars($order['code'] ?? 'N/A') ?></span></div>
      <div><strong>Phương Thức Thanh Toán:</strong> <span><?= htmlspecialchars($order['payment'] ?? 'COD') ?></span></div>
      <div><strong>Trạng Thái Thanh Toán:</strong> <span><?= htmlspecialchars($order['payment_status'] === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán') ?></span></div>
      <div><strong>Ngày Giao Dự Kiến:</strong> <span><?= date('d/m/Y', strtotime($order['delivery_date'] ?? '+3 days')) ?></span></div>
      <div><strong>Tổng Cộng:</strong> <span><?= htmlspecialchars($order['total'] ?? '0đ') ?></span></div>
      <div><strong>Trạng Thái Đơn Hàng:</strong>
        <span>
          <?php
            $status = strtolower($order['status'] ?? 'cho_xac_nhan');
            echo match($status) {
                'cho_xac_nhan' => '🕒 Chờ xác nhận',
                'dang_giao'    => '🚚 Đang giao hàng',
                'hoan_thanh'   => '✅ Hoàn thành',
                'da_huy'       => '❌ Đã hủy',
                default        => 'Không xác định'
            };
          ?>
        </span>
      </div>

    </div>
  $
    <!-- 🔹 CHI TIẾT SẢN PHẨM -->
    <div class="order-completed__details">
      <h3>Chi Tiết Sản Phẩm</h3>

      <?php if (!empty($items)): ?>
        <div class="order-completed__product-list">
          <?php foreach ($items as $item): ?>
            <div class="order-completed__product-item">
              <div class="order-completed__product-left">
                <img src="<?= htmlspecialchars($item['image_url'] ?? BASE_URL . 'Public/Assets/Image/no_image.png') ?>" 
                    alt="<?= htmlspecialchars($item['name'] ?? 'Sản phẩm') ?>">
                <div class="order-completed__product-info">
                  <p><?= htmlspecialchars($item['name'] ?? 'Tên sản phẩm') ?></p>
                  <span>Số lượng: <?= htmlspecialchars($item['quantity'] ?? 1) ?></span>
                </div>
              </div>
            <div class="order-completed__product-price">
              <?php 
                $price = $item['price'] ?? $item['unit_price'] ?? 0;
                $discount = $item['discount'] ?? 0;
                $priceAfter = $item['price_after'] ?? ($price - $discount);
              ?>
              <?php if (!empty($discount) && $discount > 0): ?>
                <span class="old-price"><?= number_format($price, 0, ',', '.') ?>đ</span>
                <span class="new-price"><?= number_format($priceAfter, 0, ',', '.') ?>đ</span>
              <?php else: ?>
                <span><?= number_format($price, 0, ',', '.') ?>đ</span>
              <?php endif; ?>
            </div>

            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="order-completed__empty">Không có sản phẩm nào trong đơn hàng này.</p>
      <?php endif; ?>
    </div>

    <!-- 🔹 NÚT HÀNH ĐỘNG -->
    <div class="order-completed__actions">
      <a href="<?= BASE_URL ?>index.php?controller=products&action=index" class="btn btn-primary">Tiếp tục mua sắm</a>
      <a href="<?= BASE_URL ?>index.php?controller=auth&action=info" class="btn btn-secondary">Xem đơn hàng của tôi</a>
    </div>
  </div>

  <?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
  <?php include_once __DIR__ . '/../../Includes/Script.php'; ?>

  <script src="<?= BASE_URL ?>Public/Assets/Js/OrderComplete.js?v=<?= time() ?>"></script>
