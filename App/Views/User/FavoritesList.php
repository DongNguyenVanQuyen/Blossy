<?php
include_once __DIR__ . '/../../Includes/config.php';

// Head
include_once __DIR__ . '/../../Includes/head.php';
include_once __DIR__ . '/../Layouts/Header.php';
?>

<!-- Trang Yêu Thích -->
<div class="wishlist">
  <div class="wishlist__header">
    <h1 class="wishlist__title">Danh Sách Yêu Thích</h1>
    <p class="wishlist__breadcrumb">Trang chủ / Yêu Thích</p>
  </div>

  <div class="wishlist__table">
    <div class="wishlist__table-head">
      <div>Sản Phẩm</div>
      <div>Giá</div>
      <div>Ngày Thêm</div>
      <div>Trạng Thái Kho</div>
      <div></div>
    </div>

    <div class="wishlist__scroll-area">
      <?php if (!empty($favorites)): ?>
        <?php foreach ($favorites as $item): ?>
          <div class="wishlist__row">
            <div class="wishlist__product">
              <img src="<?= htmlspecialchars($item['image_url'] ?? '') ?>"
                  alt="<?= htmlspecialchars($item['name']) ?>">

              <div>
                <small><?= htmlspecialchars($item['category_name'] ?? 'Đang cập nhật') ?></small>
                <p><?= htmlspecialchars($item['name']) ?></p>
              </div>
            </div>

            <div><?= number_format($item['price'], 0, ',', '.') ?>đ</div>

            <div><?= date('d/m/Y', strtotime($item['created_at'])) ?></div>

            <div class="wishlist__stock wishlist__stock--<?= ($item['stock'] ?? 0) > 0 ? 'in' : 'out' ?>">
              <?= ($item['stock'] ?? 0) > 0 ? 'Còn Hàng' : 'Hết Hàng' ?>
            </div>

            <div class="wishlist__actions">
              <button class="wishlist__add-btn <?= ($item['stock'] ?? 0) > 0 ? '' : 'disabled' ?>">Thêm Vào Giỏ</button>
              <span class="wishlist__remove" data-product-id="<?= $item['id'] ?>">&times;</span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center; padding: 30px;">Chưa có sản phẩm yêu thích nào.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="wishlist__footer">
    <div class="wishlist__link">
      <label>Liên Kết Yêu Thích:</label>
      <input type="text" value="https://www.flowrry.com" readonly>
    </div>
    <div class="wishlist__buttons">
      <button class="wishlist__copy">Sao Chép Liên Kết</button>
      <button class="wishlist__clear">Xóa Danh Sách</button>
      <button class="wishlist__addall">Thêm Tất Cả Vào Giỏ</button>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>

<script src="<?= BASE_URL ?>Public/Assets/Js/Favorites.js?v=<?= time() ?>"></script>